<?php

namespace d3yii2\d3activity\components;

use d3system\compnents\D3CommandComponent;
use d3system\controllers\D3ComponentCommandController;
use d3system\dictionaries\SysModelsDictionary;
use d3yii2\d3activity\models\D3aActivity;
use d3yii2\d3activity\models\D3aLastNotification;
use Exception;
use Yii;
use yii\validators\EmailValidator;


/**
 * @url https://github.com/d3yii2/d3system/blob/master/README.md#compnentCommands
 *
 * send daily activities of specific companies to given emails
 *
 * 'sysCompaniesEmails' = ['sysCompanyId' => 'email'];
 *
 * Component definition:
 * ```php
 *        'activityEmail' => [
 *            'class' => 'd3yii2\d3activity\components\DailyActivityNotification',
 *            'activityModelClassNames' => [
 *                'd3yii2\d3pop3\models\D3pop3Email',
 *                'dektrium\user\models\User',
 *            ],
 *            'fromMail' => 'net@company.com',
 *            //'sysCompaniesEmails' => [62 => 'uldis@nnn.lt'],
 *            'companyName' => static function(int $companyId) {
 *                if (!$company = \yii2d3\d3persons\models\D3cCompany::findOne($companyId)) {
 *                    return null;
 *                }
 *                return $company->name;
 *            },
 *            'subject' => 'Uzņēmuma {sysCompanyId} IRēķini pēdējās aktivitātes'
 *        ],
 * ```
 * component calling by command
 * yii d3system/d3-component-command activityEmail
 */
class DailyActivityNotification extends D3CommandComponent
{

    public  $sysCompaniesIds = [];

    /**
     * Use, if nod defined config parameter $getCompanyEmail
     * define companies and recipients emails.
     * @var array [
     *  $company1Id => 'email@company1.com',
     *  $company2Id => 'email1@company2.com',
     * ]
     */
    public $sysCompaniesEmails = [];

    /**
     * callable for getting emails for recipients
     * @var string|callable
     */
    public $getCompanyEmail;

    /**
     * callable for getting company name
     * @var string|callable
     */
    public $companyName;

    /**
     * Activities model class names to include in the email
     * @var array
     */
    public $activityModelClassNames = [];

    /**
     * emails sent from email.
     * @var string
     */
    public $fromMail;

    /**
     * $activityModelClassNames converted to sys_model.id
     * @var int[]
     */
    private $sysModelIds = [];

    /**
     * Email subject
     * {sysCompanyName} replace with string from parameter $companyName
     * @var string
     */
    public $subject = 'Daily {sysCompanyName} System Notifications';

    /** @var string */
    public $viewPath = '@d3yii2/d3activity/views/email/dailyActivityNotification';

    /**
     * specify if you want to select specific X previous days for query
     * if specified ignores last D3aLastNotification entry
     * @var int
     */
    public $dayRange;

    public function init()
    {
        foreach ($this->activityModelClassNames as $modelClassName) {
            $this->sysModelIds[] = SysModelsDictionary::getIdByClassName($modelClassName);
        }
        if (!$this->sysCompaniesEmails) {
            $d3aActivityQuery = D3aActivity::find()
                ->distinct()
                ->select(['sys_company_id'])
                ->andWhere(['sys_model_id' => $this->sysModelIds])
                ->andWhere('not sys_company_id is null');
            if ($this->sysCompaniesIds) {
                $d3aActivityQuery->andWhere(['sys_company_id' => $this->sysCompaniesIds]);
            }
            if ($companyActivities = $d3aActivityQuery
                ->asArray()
                ->all()
            ) {
                foreach ($companyActivities as $entry) {
                    $callable = $this->getCompanyEmail;
                    if (!is_callable($callable)) {
                        throw new \yii\console\Exception('setting getCompanyEmail must be callable');
                }
                    $this->sysCompaniesEmails[$entry['sys_company_id']] = $callable($entry['sys_company_id']);
                }
            }
        }
    }

    public function run(D3ComponentCommandController $controller): bool
    {
        parent::run($controller);
        if (empty($this->sysCompaniesEmails)) {
            return true;
        }
        foreach ($this->sysCompaniesEmails as $companyId => $emails) {
            foreach ($emails as $email) {
                $this->out('SysCompnay: ' . $companyId . ';  to: ' . $email);
                $emailValidator = new EmailValidator();
                if (empty($companyId)) {
                    $this->out(' Empty companyId - continue');
                    continue;
                }
                if (empty($email)) {
                    $this->out(' Empty email - continue');
                    continue;
                }
                if (!$emailValidator->validate($email)) {
                    $this->out(' Invalid email - continue');
                    continue;
                }
                if (!is_int($companyId)) {
                    $this->out(' Invalid companyId - continue');
                    continue;
                }

                $d3aActivityQuery = D3aActivity::find()
                    ->where(['sys_company_id' => $companyId])
                    ->andWhere(['sys_model_id' => $this->sysModelIds]);
                if (!empty($this->dayRange)) {
                    $d3aActivityQuery
                        ->andWhere(['>', 'time', date('Y-m-d 00:00:00', strtotime('-'.$this->dayRange.' days'))]);
                } else if ($lastNotifications = D3aLastNotification::find()
                    ->where(['sys_company_id' => $companyId])
                    ->orderBy(['time' => SORT_DESC])
                    ->one()
                ) {
                    $d3aActivityQuery
                        ->andWhere(['>', 'time', $lastNotifications->time]);
                }

                if (!$newActivities = $d3aActivityQuery->all()) {
                    $this->out(' No activities - continue');
                    continue;
                }
                $this->out(' Found ' . count($newActivities));

                if (is_callable($this->companyName)) {
                    $callable = $this->companyName;
                    $companyName = $callable($companyId);
                } else {
                    $companyName = $this->companyName;
                }
                if ($this->composeEmail($newActivities, $email, $companyName)) {
                    $this->out(' Sent');
                    $this->logSentActivities($companyId);
                }
            }
        }

        return true;
    }

    private function composeEmail(array $newActivities, string $emailTo, string $companyName): bool
    {

        try {

            $html = Yii::$app
                ->controller
                ->renderPartial(
                    $this->viewPath,
                    [
                        'activities' => $newActivities,
                        'companyName' => $companyName
                    ]
                );

            Yii::$app
                ->mailer
                ->compose()
                ->setTo($emailTo)
                ->setFrom($this->fromMail)
                ->setSubject(str_replace('{sysCompanyName}', $companyName, $this->subject))
                ->setHtmlBody($html)
                ->send();

            return true;
        } catch (Exception $e) {
            $this->out($e->getMessage());
            $this->out($e->getTraceAsString());
            Yii::error($e->getMessage());
            Yii::error($e->getTraceAsString());
        }
        return false;
    }

    private function logSentActivities(int $sysCompanyId): void
    {
        $connection = $this->controller->getConnection();
        if (!$transaction = $connection->beginTransaction()) {
            throw new \yii\db\Exception('Can not initiate transaction');
        }
        $model = new D3aLastNotification();
        $model->sys_company_id = $sysCompanyId;
        try {

            $model->save();
            $transaction->commit();
        } catch (Exception $e) {

            $this->out($e->getMessage());
            $this->out($e->getTraceAsString());
            Yii::error($e->getMessage());
            Yii::error($e->getTraceAsString());
            $transaction->rollback();
        }
    }
}
