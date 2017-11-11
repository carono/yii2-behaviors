<?php


namespace carono\yii2behaviors;


use carono\yii2rbac\CurrentUser;
use carono\yii2rbac\RoleManager;
use yii\base\Component;
use yii\caching\Cache;
use yii\db\ActiveRecord;
use yii\di\Instance;
use yii\rbac\BaseManager;

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
     * @var string|BaseManager
     */
    public $authManager = 'authManager';
    /**
     * @var Cache
     */
    public $cache;

    public $cacheKey = 'url-rule-behavior';

    public $duration;

    public $dependency;

    /**
     * @var ActiveRecord
     */
    public $model;

    protected function haveRole($role, $user)
    {
        return in_array($role, $this->getRoles($user));
    }

    public function init()
    {
        $this->authManager = Instance::ensure($this->authManager, BaseManager::className());
        $this->cache = $this->cache ?: (!empty($this->authManager->cache) ? $this->authManager->cache : null);
        if ($this->cache) {
            $this->cache = Instance::ensure($this->cache, Cache::className());
        }
    }

    protected function setCache($key, $value)
    {
        if ($this->cache) {
            $this->cache->set($key, $value, $this->duration, $this->dependency);
        }
    }

    protected function getCache($key)
    {
        return $this->cache ? $this->cache->get($key) : null;
    }

    protected function getRoles($user)
    {
        if ($roles = $this->getCache([$this->cacheKey, $user->id])) {
        } else {
            $roles = array_keys($this->authManager->getRolesByUser($user->id));
            $this->setCache([$this->cacheKey, $user->id], $roles);
        }
        return $roles;
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
                    if ($this->haveRole($role, $user) || in_array($role, $this->getRoles($user))) {
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