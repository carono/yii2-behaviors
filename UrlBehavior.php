<?php

namespace carono\yii2behaviors;

use yii\base\Behavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class UrlBehavior extends Behavior
{
    protected $_urlRules = [];

    public $rules = [];
    public $functionAlias = 'getUrl';

    protected function getUrlRules()
    {
        return is_string($this->rules) ? call_user_func([$this->owner, $this->rules]) : $this->rules;
    }

    /**
     * @return UrlRule[]
     */
    protected function normalizeUrlRules()
    {
        /**
         * @var $urlRule UrlRule
         */
        if ($this->_urlRules) {
            return $this->_urlRules;
        }
        foreach ($this->getUrlRules() as $rule) {
            $action = ArrayHelper::remove($rule, 0);
            $rule['action'] = $action;
            $rule['class'] = UrlRule::className();
            $rule['model'] = $this->owner;
            $urlRule = \Yii::createObject($rule);
            $this->_urlRules[] = $urlRule;
        }
        return $this->_urlRules;
    }

    public function __call($name, $arguments)
    {
        if ($name === $this->functionAlias) {
            return call_user_func_array([$this, 'getUrl'], $arguments);
        }
        return parent::__call($name, $arguments);
    }

    public function hasMethod($name)
    {
        return $name === $this->functionAlias;
    }

    /**
     * @param $action
     * @param bool $asString
     * @return array|mixed|string
     */
    protected function getUrl($action, $asString = false)
    {
        $url = [];
        foreach ($this->normalizeUrlRules() as $rule) {
            if ($rule->compare($action, \Yii::$app->user->getIdentity())) {
                $url = is_callable($rule->url) ? call_user_func($rule->url, $this->owner) : $rule->url;
                break;
            }
        }
        return $asString ? Url::to($url, true) : $url;
    }
}