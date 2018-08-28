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
        $res = json_encode($this);
        clog($_SERVER["REQUEST_URI"]);
        clog($res);
        die($res);
    }
    public function hecho(){
        $res = json_encode($this);
        clog($_SERVER["REQUEST_URI"]);
        clog($res);
        echo $res;
    }
}