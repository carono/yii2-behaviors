<?php

namespace carono\yii2behaviors;

use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class AuthorBehavior
 *
 * @package carono\yii2behaviors
 * @deprecated see Blame
 */
class AuthorBehavior extends AttributeBehavior
{
    public $attributes;
    public $createdByAttribute = 'creator_id';
    public $updatedByAttribute = 'updater_id';

    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdByAttribute, $this->updatedByAttribute],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedByAttribute,
            ];
        }
    }

    /**
     * @inheritdoc
     */
    protected function getValue($event)
    {
        if ($event->name == BaseActiveRecord::EVENT_BEFORE_INSERT) {
            $attr = ArrayHelper::getValue(
                $this->attributes, BaseActiveRecord::EVENT_BEFORE_INSERT, $this->createdByAttribute
            );
            if (is_array($attr)) {
                $attr = current($attr);
            }
            if ($value = $event->sender->{$attr}) {
                return $value;
            }
        }
        if (isset(\Yii::$app->user)) {
            return \Yii::$app->user->getId();
        } else {
            return null;
        }
    }
}