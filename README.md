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

## defining components
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
        Yii::$app->activityList->filter = [ActivityConfig::PERSON_ID => 44];
        $list = Yii::$app
            ->activityList
            ->getDescList([$sysModelIdA,$sysModelIdB]);

```


## Examples
