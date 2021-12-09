<?php

namespace d3yii2\d3activity\tests\components;

use d3system\dictionaries\SysModelsDictionary;
use d3yii2\d3activity\components\D3ActivityList;
use d3yii2\d3activity\components\DbActivityRegistar;
use d3yii2\d3activity\dictionaries\D3aActionDictionary;
use d3yii2\d3activity\models\D3aActivity;
use PHPUnit\Framework\TestCase;
use Yii;

class D3ActivityListTest extends TestCase
{

    public const USER_ID = 7; //Lauris Nalivvaiko
    public const TEST_ACTION = 'test/test';
    const TEST_CLASS_NAME = 'TestClassName';
    const MODEL_Id = 77;
    static public $companyId = 14;


    public static function setUpBeforeClass()
    {
        $class = Yii::$app->user->identityClass;
        $identity = $class::findIdentity(self::USER_ID);
        Yii::$app->SysCmp->setActiveId(self::$companyId);
        Yii::$app->user->switchIdentity($identity);
        self::clearData();
    }

    private static function clearData(): void
    {
        foreach (D3aActivity::findAll([
            'action_id' => D3aActionDictionary::getIdByName(self::TEST_ACTION)])
                 as $record
        ) {
            $record->delete();
        }
    }

    public static function tearDownAfterClass()
    {
        self::clearData();
    }

    public function testRegister(): void
    {
        $register = new DbActivityRegistar([
            'sysCompanyId' => static function () {
                return Yii::$app->SysCmp->getActiveCompanyId();
            },
            'userId' => static function () {
                return Yii::$app->user->id;
            }
        ]);

        $testModel = new PeriodWorkTimeTest();
        $testModel->id = self::MODEL_Id;

        $register->registerModel($testModel, self::TEST_ACTION, ['test' => 'test']);

        $cnt = D3aActivity::find()
            ->where(['action_id' => D3aActionDictionary::getIdByName(self::TEST_ACTION)])
            ->count();
        $this->assertEquals(1, $cnt);

        $register->registerClasNameId(self::TEST_CLASS_NAME, self::MODEL_Id, self::TEST_ACTION, ['test' => 'test']);
        $cnt = D3aActivity::find()
            ->where([
                'action_id' => D3aActionDictionary::getIdByName(self::TEST_ACTION),
                'model_id' => self::MODEL_Id
            ])
            ->count();
        $this->assertEquals(2, $cnt);
    }

    /**
     * @depends testRegister
     */
    public function testList()
    {
        $activitylist = new D3ActivityList([
            'sysCompanyId' => static function () {
                return Yii::$app->SysCmp->getActiveCompanyId();
            },
            'modelsData' => [
                [
                    'modelClass' => PeriodWorkTimeTest::class,
                    'detailClass' => TestModelD3Activity::class
                ],
                [
                    'modelClass' => self::TEST_CLASS_NAME,
                    'detailClass' => TestModelD3Activity::class
                ]
            ],
        ]);
        $sysModelIdA = SysModelsDictionary::getIdByClassName(PeriodWorkTimeTest::class);
        $sysModelIdB = SysModelsDictionary::getIdByClassName(self::TEST_CLASS_NAME);
        $list = $activitylist->getDescList([$sysModelIdA]);
        $this->assertCount(1, $list);
        $list = $activitylist->getDescList([$sysModelIdA, $sysModelIdB]);
        $this->assertCount(2, $list);
    }
}
