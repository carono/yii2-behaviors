<?php

namespace carono\yii2behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class ModelAttributeEventBehaviour
 *
 * Поведение для создания событий при изменении атрибутов у модели
 * Необходимо создать новый клас, наследованный от этого поведения и прикрепить к модели
 * После этого, в новом поведелии, можно вписывать функции по типу onChange{имя_атрибута}, и при изменеии именного
 * этого аттрибута, сработает эта фукцния.
 * Входящие переменные функции:
 *
 * AfterSaveEvent $event - евент
 * boolean $insert - значение, была ли создана модель или изменена, по аналогии с afterSave
 * ActiveRecord $model - сама модель
 * mixed $value - новое значение аттрибута
 * mixed $oldValue - старое значение аттрибуте
 * array $changedAttributes - список всех изменяемых атрибутов у модели
 *
 * !ВАЖНО В $changedAttributes находятся старые значение аттрибутов, а не новые
 *
 * Пример
 *
 * public function onChangeStatus_id($event, $insert, $model, $value, $oldValue, $changedAttributes)
 * {
 *   // Событие, при изменении аттрибута status_id у модели
 * }
 *
 * @package app\components\behaviors
 */
class ModelAttributeEventBehaviour extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'onAfterUpdate',
            ActiveRecord::EVENT_AFTER_INSERT => 'onAfterInsert'
        ];
    }

    public function onAfterUpdate($event)
    {
        $this->event($event, false, 'onChange');
    }

    public function onAfterInsert($event)
    {
        $this->event($event, true, 'onInsert');
    }

    protected function event($event, $insert, $methodPrefix)
    {
        /**
         * @var ActiveRecord $owner
         */
        $changedAttributes = $event->changedAttributes;
        foreach ($changedAttributes as $attribute => $oldValue) {
            $method = $methodPrefix . ucfirst($attribute);
            $owner = $this->owner;
            if (method_exists($this, $method)) {
                $newValue = $owner->getAttribute($attribute);
                call_user_func([$this, $method], $event, $insert, $owner, $newValue, $oldValue, $changedAttributes);
            }
        }
    }
}