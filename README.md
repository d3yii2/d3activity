# Activity registry"
Registre models activities and get activity lists
## Features

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ composer require d3yii2/d3activity "*"
```

or add

```
"d3yii2/d3activity": "*"
```

to the `require` section of your `composer.json` file.

## DB
![DB strukture](https://github.com/d3yii2/d3activity/blob/master/doc/DB.png)

## Defining component
Register and list 
```php
    'components' => [
        'activityList' => [
            'class' => 'd3yii2\d3activity\components\D3ActivityList',
            'sysCompanyId' => static function () {
                return \Yii::$app->SysCmp->getActiveCompanyId();
            },
            'models' => [
                [
                    'class' => 'd3modules\d3invoices\models\InvInvoice',
                    'detailClass' => 'd3modules\d3invoices\components\InvInvoiceD3Activity'
                ]
            ],
        ],
        'activityRegistar' => [
            'class' => 'd3yii2\d3activity\components\DbActivityRegistar',
            'sysCompanyId' => static function () {
                return \Yii::$app->SysCmp->getActiveCompanyId();
            },
            'userId' => static function () {
                return \Yii::$app->user->id;
            }            
            
        ],
    ]
```

Emailing last activities for some companies

```php
 'components' =>
    [
        'activityEmail' => [
            'class' => 'd3yii2\d3activity\components\DailyActivityNotification',
            /** filter activities by models */
            'activityModelClassNames' => [
                'd3yii2\d3pop3\models\D3pop3Email',
                'dektrium\user\models\User',
            ],
            'fromMail' => 'net@irekini.lv',
            /** limitation for comanies.  */
            'sysCompaniesEmails' => [62 => 'uldis@weberp.lv'],
            'companyName' => static function(int $companyId) {
                if (!$company = \yii2d3\d3persons\models\D3cCompany::findOne($companyId)) {
                    return null;
                }
                return $company->name;
            },
            'subject' => 'Company {sysCompanyName} activities'
        ],
    ]
```

Emailing last activities for multiple companies

```php
 'components' =>
    [
        'activityEmail' => [
            'class' => 'd3yii2\d3activity\components\DailyActivityNotification',
            /** filter activities by models */
            'activityModelClassNames' => [
                'd3yii2\d3pop3\models\D3pop3Email',
                'dektrium\user\models\User',
            ],
            'fromMail' => 'net@irekini.lv',
            /** limitation for comanies.  */
            //'sysCompaniesIds' => [62],
            'companyName' => static function(int $companyId) {
                if (!$company = \yii2d3\d3persons\models\D3cCompany::findOne($companyId)) {
                    return null;
                }
                return $company->name;
            },
            'getCompanyEmail' => static function(int $companyId) {
                    $sql = '
                SELECT
                    u.email
                FROM
                    user u
                    LEFT OUTER JOIN auth_assignment aa
                      ON aa.user_id = u.id
                WHERE
                    aa.sys_company_id = :id
                    AND aa.item_name = :item_name
                    LIMIT 1
                ';

                    $param = [
                        ':id' => $companyId,
                        ':item_name' => 'CompanyOwner',
                    ];

                    return Yii::$app
                        ->getDb()
                        ->createCommand($sql, $param)
                        ->queryColumn();
            },
            'subject' => 'Company {sysCompanyName} activities'
        ],
    ]
```


## Usage
Registr eactivity
```php
    Yii::$app
        ->activityRegistar
        ->registerModel(
            $model,
            $this->route,
            ArrayHelper::filter($deliveryModel->attributes,[
                'recipient_person'
            ])
        );
```

Get activity record list

```php
        $sysModelIdA = SysModelsDictionary::getIdByClassName(TestModel::class);
        $sysModelIdB = SysModelsDictionary::getIdByClassName(self::TEST_CLASS_NAME);
        Yii::$app->activityList->filter = [ActivityConfig::PARTNER_ID => 44];
        $list = Yii::$app
            ->activityList
            ->getDescList([$sysModelIdA,$sysModelIdB]);

```
