<?php


namespace d3yii2\d3activity\components;

/**
 * use in activity lists as record
 * Class ActivityRecord
 * @package d3yii2\d3activity\components
 */
class ActivityRecord
{
    /** @var string */
    public $name;

    /** @var string */
    public $label;

    /** @var array */
    public $url;

    /** @var int */
    public $recordId;

    /** @var \DateTime */
    public $dateTime;

    /** @var array */
    public $additionalFields = [];

    /** @var int */
    public $userId;

    /** @var int */
    public $sysModelId;

}