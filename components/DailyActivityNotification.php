<?php

namespace d3yii2\d3activity\components;

use d3system\commands\D3CommandController;
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
 *      'sysCompanyIds' => [1 => 'email1@email1.com', 2 => 'email2@email2.com', 3 => ....],
 *      'sysModelIds' => [1,2,3,4,5]
 *  ]
 */

class DailyActivityNotification extends D3CommandController {

    /** @var array */
    public $sysCompanyIds = [];

    /** @var array */
    public $sysModelIds = [];

    /** @var string */
    public $subject = 'Daily System Notification';

    /** @var string */
    public $fromEmail = 'info@system.com';

    public function actionIndex() : int
    {
        if(!empty($this->sysCompanyIds)) {
            foreach ($this->sysCompanyIds as $companyId => $email) {
                $emailValidator = new EmailValidator();
                if(empty($companyId) || empty($email) || !$emailValidator->validate($email) || !is_int($companyId)) {
                    continue;
                }
               if($lastNotifications =  D3aLastNotification::find()->where(['sys_company_id' => $companyId])->orderBy(['time' => SORT_DESC])->one()) {
                   if($newActivities =  D3aActivity::find()->where(['sys_company_id' => $companyId])->andWhere(['sys_model_id' => $this->sysModelIds])->andWhere(['>', 'time', $lastNotifications->time])->all()) {
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
            $html = Yii::$app->controller->renderPartial('@d3yii2/d3activity/views/email/dailyActivityNotification',['activities' => $newActivities]);
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
}
