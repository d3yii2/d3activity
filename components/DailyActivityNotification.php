<?php

namespace d3yii2\d3activity\components;

use d3system\commands\D3CommandController;
use d3system\dictionaries\SysModelsDictionary;
use d3yii2\d3activity\models\D3aActivity;
use d3yii2\d3activity\models\D3aLastNotification;
use d3yii2\d3pop3\components\D3Mail;
use yii\console\ExitCode;
use yii\validators\EmailValidator;
use Yii;


/**
 * send daily activities of specific companies to given emails
 *
 * $sysCompanyIds = ['sysCompanyId' => 'email'];
 *
 * Component definition:
 * ```php
 *  'dailyActivityNotification' =>[
 *      'class' => 'd3yii2\components\DailyActivityNotification',
 *      'sysCompanyIds' => [1 => 'd3yii2\d3pop3\models\D3pop3Email', 2 => 'dektrium\user\models\User', 3 => ....],
 *      'sysModelClassNames' => ['yii2d3\d3persons\accessRights\CompanyOwnerUserRole']
 *  ]
 */

class DailyActivityNotification extends D3CommandController {

    /** @var array */
    public $sysCompanyIds = [];

    /** @var array */
    public $sysModelClassNames = [];
    private $sysModelIds = [];

    /** @var string */
    public $subject = 'Daily System Notification';

    /** @var string */
    public $fromEmail = 'info@system.com';

    /** @var string */
    public $viewPath = '@d3yii2/d3activity/views/email/dailyActivityNotification';

    /** @var array  */
    public $userRoles = [];
    private $userRoleNames = [];

    public function init()
    {
        if(empty($this->sysCompanyIds) && !empty($this->userRoles)) {
            foreach ($this->sysModelClassNames as $key => $modelClassName) {
                $this->sysModelIds[] = SysModelsDictionary::getIdByClassName($modelClassName);
            }
            foreach ($this->userRoles as $key => $rolePath) {
                if(class_exists($rolePath) && $rolePath::NAME !== null) {
                    $this->userRoleNames[] = $rolePath::NAME;
                }
            }

            if($companyActivities = D3aActivity::find()
                ->distinct()
                ->select(['sys_company_id'])
                ->andWhere(['sys_model_id' => $this->sysModelIds])
                ->asArray()
                ->all()) {
                foreach ($companyActivities as $key => $entry) {
                    $owner = $this->getUserEmailBYRole($entry['sys_company_id']);
                    if(isset($owner[0])) {
                        $this->sysCompanyIds[$entry['sys_company_id']] = $owner[0]['email'];
                    }
                }
            }
        }
    }

    public function actionIndex() : int
    {
        if(!empty($this->sysCompanyIds)) {
            foreach ($this->sysCompanyIds as $companyId => $email) {
                $emailValidator = new EmailValidator();
                if(empty($companyId) || empty($email) || !$emailValidator->validate($email) || !is_int($companyId)) {
                    continue;
                }
               if($lastNotifications =  D3aLastNotification::find()->where(['sys_company_id' => $companyId])->orderBy(['time' => SORT_DESC])->one()) {
                   if($newActivities =  D3aActivity::find()
                       ->where(['sys_company_id' => $companyId])
                       ->andWhere(['sys_model_id' => $this->sysModelIds])
                       ->andWhere(['>', 'time', $lastNotifications->time])
                       ->all()) {
                        if($this->composeEmail($newActivities, $email, $companyId)) {
                            $this->logSentActivities($companyId);
                        }
                   }
               } else {

                   if($newActivities =  D3aActivity::find()->where(['sys_company_id' => $companyId])->andWhere(['sys_model_id' => $this->sysModelIds])->all()) {
                       if($this->composeEmail($newActivities, $email, $companyId)) {
                           $this->logSentActivities($companyId);
                       }
                   }
               }
            }

            return ExitCode::OK;
        }

        return ExitCode::NOINPUT;
    }

    private function composeEmail(array $newActivities, string $emailTo, int $companyId): bool
    {
        $connection  = $this->getConnection();
        $transaction = $connection->beginTransaction();
        try {
            $email = new D3Mail();
            $html = Yii::$app->controller->renderPartial($this->viewPath,['activities' => $newActivities]);
            $email->setBodyHtml($html)
                ->setEmailId(['SYS', $companyId, 'INV', $newActivities[0]->id, date('YmdHis')])
                ->setSubject($this->subject)
                ->setFromEmail($this->fromEmail)
                ->addAddressTo($emailTo)
                ->addSendReceiveToInCompany($companyId)
                ->save();

                $email->send();
                $transaction->commit();
                return true;
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            Yii::error($e->getTraceAsString());
            $transaction->rollback();
        }
        return false;
    }

    private function logSentActivities(int $sysCompanyId): void
    {
        $connection  = $this->getConnection();
        $transaction = $connection->beginTransaction();
        try {
            $model = new D3aLastNotification();
            $model->sys_company_id = $sysCompanyId;
            $model->save();
            $transaction->commit();
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            Yii::error($e->getTraceAsString());
            $transaction->rollback();
        }
    }

    private function getUserEmailBYRole(int $companyId)
    {
        $sql = '
            SELECT
                u.email,
                u.username,
                u.id
            FROM
                user u
                LEFT OUTER JOIN auth_assignment aa
                  ON aa.user_id = u.id
            WHERE
                aa.sys_company_id = :id
                AND aa.item_name IN (:item_name)
                LIMIT 1
            ';

        $param = [
            ':id' => $companyId,
            ':item_name' => implode(",", $this->userRoleNames),
        ];

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql, $param);
        return $command->queryAll();
    }
}
