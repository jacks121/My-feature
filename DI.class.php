<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 2017/8/24
 * Time: 下午2:36
 */
class DI{

    public $obj = [];

    public function set($objName, $callback)
    {
        $this->obj = array_merge($this->obj,[$objName => new $callback]);
    }

    public function make($objName)
    {
        return $this->obj[$objName];
    }
    
}