<?php
/**
 * Created by PhpStorm.
 * User: 吴宏佳
 * Date: 2018/8/28
 * Time: 上午8:47
 */
require_once BASEPATH."/lib/log.php";

function currentDirectory() {

    $str = $_SERVER["PHP_SELF"];

    $arr = explode("/",$str);
    array_pop($arr);
    return implode("/",$arr)."/";

}

function sendTransferMsg($arr){
    clog("whj05968798");


    $params = "";
    foreach($arr as $k=>$v){ $params .= $k . '=' . $v . "&"; }
    $params = rtrim($params , '&');

    return postText("http://" . $_SERVER["HTTP_HOST"] . ":38081/", "http://" . $_SERVER["HTTP_HOST"] . currentDirectory() ."transferUpdate?" . $params);

}

function postText($url,$text){
    $headers[] = "Content-type: text/plain";//定义content-type为text

    return postCC($url,$text,$headers);
}
function post($url, $post_data = array()){
    clog($url . " | " . toString($post_data));

    return postCC($url,$post_data,null);
}
function postCC($url, $post_data = array(),$headers = array())
{
    clog($url . " | " . toString($post_data));

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_POST, 1);

    if (count($headers) > 0){

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);//定义请求类型
    }
    if ($post_data) {

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    curl_setopt($ch, CURLOPT_HEADER, false);

    $file_contents = curl_exec($ch);

    curl_close($ch);

    return $file_contents;

}

function get($url)
{

    clog($url);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);


    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    curl_setopt($ch, CURLOPT_HEADER, false);

    $file_contents = curl_exec($ch);

    curl_close($ch);


    return $file_contents;

}