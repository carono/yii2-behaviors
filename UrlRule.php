<?php


namespace carono\yii2behaviors;


use carono\yii2rbac\CurrentUser;
use carono\yii2rbac\RoleManager;
use yii\base\Component;
use yii\db\ActiveRecord;

/**
 * Class UrlRule
 *
 * @property mixed action
 * @property mixed url
 * @package app\components
 */
class UrlRule extends Component
{
    protected $_original_action;
    protected $_action;
    protected $_modifier;
    protected $_url;
    public $role;
    public $application;
    public $params = [];
    /**
     * @var ActiveRecord
     */
    public $model;

    protected static function haveRole($role, $user)
    {
        return in_array($role, self::getRoles($user));
    }

    protected static function getRoles($user)
    {
        return array_keys(\Yii::$app->authManager->getRolesByUser($user->id));
    }

    /**
     * @param $action
     * @param $user
     * @return bool
     */
    public function compare($action, $user)
    {
        $needApplication = \Yii::$app->id == $this->application || !$this->application;
        $needRole = false;
        $needAction = $action == $this->_original_action;
        if ($this->role) {
            if ($user) {
                foreach ($this->role as $role) {
                    if (self::haveRole($role, $user) || in_array($role, self::getRoles($user))) {
                        $needRole = true;
                        break;
                    }
                }
            }
        } else {
            $needRole = true;
        }
        return $needAction && $needApplication && $needRole;
    }

    public function setUrl($value)
    {
        $this->_url = $value;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        $url = $this->_url;
        if (is_string($url)) {
            $url = [$url];
        }
        foreach ($this->params as $key => $param) {
            $url[is_numeric($key) ? $param : $key] = $this->model->getAttribute($param);
        }
        return $url;
    }

    public function setAction($action)
    {
        $this->_original_action = $action;
        $arr = explode('/', $action);
        $this->_action = $arr[0];
        $this->_modifier = count($arr) == 2 ? $arr[1] : null;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function getModifier()
    {
        return $this->_modifier;
    }
}