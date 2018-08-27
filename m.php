<?php
/**
 * Author：helen
 * CreateTime: 2016/07/27 10:26
 * Description：
 */
// 权限控制
//include_once './auth.php';

// 项目根路径
define('BASEPATH', dirname(__FILE__));
// 调试模式
define('DEBUG', True);

require_once BASEPATH."/lib/config.php";

// 应用入口文件
date_default_timezone_set("PRC");
header('Content-type: text/html;charset=utf-8');
if (DEBUG){
    header("Access-Control-Allow-Origin: *");
}



require_once BASEPATH."/api".dirname($_SERVER["PATH_INFO"]).".php";