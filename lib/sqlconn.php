<?php
/**
 * Created by PhpStorm.
 * User: 吴宏佳
 * Date: 2018/8/23
 * Time: 下午2:17
 */

function sqlconn()
{

    $resp = new BaseResp();


    if (DEBUG){
        $host = "localhost";
        $username = "root";
        $passwd = "qwertyui";
        $dbname = "points";
    }


    // 创建连接
    $conn = new mysqli($host, $username, $passwd, $dbname);
    // 检测连接
    if ($conn->connect_error) {
        $resp->msg = $conn->connect_error;
        $resp->ecode = 3001;
        $resp->hdie();
    }
    $conn->query("set names utf8;");
    return $conn;
}