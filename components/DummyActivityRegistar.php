<?php

namespace d3yii2\d3activity\components;

use yii\base\Component;


class DummyActivityRegistar extends Component implements ActivityRegistar
{


    public function registerModel(object $model, string $action, array $data = []): void
    {
    }

    public function registerClasNameId(string $className, int $id, string $action, array $data = []): void
    {
    }

}
