<?php
/**
 * Created by PhpStorm.
 * User: wuhongjia
 * Date: 2018/8/18
 * Time: 下午3:18
 */

class BaseResp
{
    public $ok;
    public $msg;
    public $info;
    public $list;
    public $code;

    public function __construct()
    {
        $this->ok = false;
        $this->msg = "";
        $this->info = new  stdClass();
        $this->list = [];
        $this->code = 200;
    }
    public function hdie(){
        die(json_encode($this));
    }
    public function hecho(){
        echo json_encode($this);
    }
}