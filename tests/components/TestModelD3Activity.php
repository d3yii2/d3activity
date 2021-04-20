<?php


namespace d3yii2\d3activity\tests\components;


use d3yii2\d3activity\components\ActivityRecord;
use d3yii2\d3activity\components\ModelActivityInterface;

class TestModelD3Activity implements ModelActivityInterface
{
    /**
     * @param int[] $idList
     * @return ActivityRecord[]
     */
    public static function findByIdList(array $idList): array
    {
        $record = new ActivityRecord();
        $record->recordId = D3ActivityListTest::MODEL_Id;
        $record->label = 'record label';
        $record->url = [
            '/invoices/inv-invoice/view',
            'id' => 77
        ];

        return [$record];
    }
}