<?php


namespace carono\yii2behaviors;

use yii\helpers\StringHelper;
use yii\base\Behavior;

/**
 * Class ModelEventBehavior
 *
 * @package app\components
 */
class ModelEventBehavior extends Behavior
{
    /**
     * @return array
     */
    public function events()
    {
        $events = [];
        foreach ((new \ReflectionClass($this))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (StringHelper::startsWith($method->name, 'on')) {
                $events[lcfirst(substr($method->name, 2))] = $method->name;
            }
        }
        return $events;
    }
}