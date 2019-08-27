<?php
/**
 * Created by PhpStorm.
 * User: 吴宏佳
 * Date: 2018/8/23
 * Time: 下午4:17
 */
require_once BASEPATH . "/lib/sqlconn.php";
require_once BASEPATH . "/lib/net.php";


function randomCard($len)
{
    $pattern = '0123456789' . time();
    $res = "";
    for ($num = 0; $num < $len; $num++) {
        $res = $res . $pattern[rand(0, strlen($pattern) - 1)];
    }
    return $res;
}

function addcard()
{
    $resp = new  BaseResp();
    if (empty($_GET["org_id"])) {
        $resp->msg = "请填写组织机构唯一标示org_id";
        $resp->code = 4003;
        $resp->hdie();
    }
    if (empty($_GET["usercode"])) {
        $resp->msg = "请填写用户唯一标示usercode";
        $resp->code = 4000;
        $resp->hdie();
    }
    if (empty($_GET["pay_pwd"])) {
        $resp->msg = "请填写用户支付密码pay_pwd";
        $resp->code = 4001;
        $resp->hdie();
    }

    if (empty($_GET["coin_code"])) {
        $resp->msg = "请填写币唯一标示coin_code";
        $resp->code = 4002;
        $resp->hdie();
    }
    $conn = sqlconn();
    $prefix = md5($_GET["org_id"]);
    $usercode = $_GET["usercode"];
    $pay_pwd = md5($_GET["pay_pwd"]);
    $coin_code = $_GET["coin_code"];
    $card = $coin_code . randomCard(16);

    $sql = "SELECT id FROM `{$prefix}_coin` WHERE code='{$coin_code}' LIMIT 1";
    $res = $conn->query($sql);
    if ($res){
        if ($res->num_rows == 0){
            $resp->msg = "币不存在";
            $resp->code = 4106;
            $resp->hdie();
        }
    }else{
        $resp->msg = "币查询失败";
        $resp->code = $_GET["org_id"];
        $resp->hdie();
    }

    $sql = "SELECT card FROM `{$prefix}_card` WHERE usercode='{$usercode}'  AND coin_code='{$coin_code}' LIMIT 1";
    $res = $conn->query($sql);

    if ($res) {
        $row = $res->fetch_assoc();
        if (empty($row["card"])) {

            $sql = "INSERT INTO `{$prefix}_card` (usercode,pay_pwd ,card,coin_code, balance,effe ) VALUES ('{$usercode}','{$pay_pwd}','{$card}','{$coin_code}',0,TRUE )";
            $res1 = $conn->query($sql);

            if ($res1) {
                $resp->ok = true;
                $resp->card = $card;
                $resp->hecho();
            } else {
                $resp->msg = "创建币账户失败";
                $resp->code = 4005;
                $resp->hdie();
            }


        } else {
            $resp->ok = true;
            $resp->card = $row["card"];
            $resp->hecho();

        }
        $res->free_result();

    } else {
        $resp->msg = "创建币账户失败";
        $resp->code = 4006;
        $resp->hdie();
    }
    $conn->close();


}

function transfer()
{
    $resp = new  BaseResp();
    if (empty($_GET["org_id"])) {
        $resp->msg = "请填写组织机构唯一标示org_id";
        $resp->code = 4013;
        $resp->hdie();
    }
    if (empty($_GET["usercode"])) {
        $resp->msg = "请填写usercode";
        $resp->code = 4007;
        $resp->hdie();
    }
    if (empty($_GET["pay_pwd"])) {
        $resp->msg = "请填写pay_pwd";
        $resp->code = 4008;
        $resp->hdie();
    }
    if (empty($_GET["card"])) {
        $resp->msg = "请填写card";
        $resp->code = 4009;
        $resp->hdie();
    }

    if (empty($_GET["tcard"])) {
        $resp->msg = "请填写tcard";
        $resp->code = 4010;
        $resp->hdie();
    }
    if (empty($_GET["amount"])) {
        $resp->msg = "请填写amount";
        $resp->code = 4011;
        $resp->hdie();
    }
    if (empty($_GET["coin_code"])) {
        $resp->msg = "请填写币唯一标示coin_code";
        $resp->code = 4012;
        $resp->hdie();
    }
    if (empty($_GET["backurl"])) {
        $resp->msg = "请填写backurl";
        $resp->code = 4052;
        $resp->hdie();
    }
    if (strpos($_GET["backurl"],'?') ){
        $resp->msg = "backurl 不可以包含？";
        $resp->code = 4062;
        $resp->hdie();
    }


    $conn = sqlconn();
    $prefix = md5($_GET["org_id"]);
    $usercode = $_GET["usercode"];
    $pay_pwd = md5($_GET["pay_pwd"]);
    $coin_code = $_GET["coin_code"];
    $fcard = $_GET["card"];
    $tcard = $_GET["tcard"];
    $amount = $_GET["amount"];

    $sql = "SELECT balance FROM `{$prefix}_card` WHERE effe=TRUE AND usercode='{$usercode}'  AND coin_code='{$coin_code}' AND card='{$fcard}' AND pay_pwd = '{$pay_pwd}'  LIMIT 1";
    $res = $conn->query($sql);

    $balance = 0;
    if ($res) {
        if ($res->num_rows == 0) {
            $resp->msg = "卡号或密码不正确";
            $resp->code = 4014;
            $resp->hdie();
        }
        $balance = $res->fetch_assoc()["balance"];
        $res->free_result();

    } else {
        if ($res->num_rows == 0) {
            $resp->msg = "card查询失败";
            $resp->code = 4015;
            $resp->hdie();
        }
    }


    if ($balance < $amount) {
        $resp->msg = "卡余额不足";
        $resp->code = 4036;
        $resp->hdie();
    }


    $sql = "SELECT id FROM `{$prefix}_card` WHERE effe=TRUE AND  coin_code='{$coin_code}' AND card='{$tcard}'   LIMIT 1";
    $res = $conn->query($sql);
    if ($res) {
        if ($res->num_rows == 0) {
            $resp->msg = "目标card不存在";
            $resp->code = 4016;
            $resp->hdie();
        }
        $res->free_result();

    } else {
        $resp->msg = "目标card查询失败";
        $resp->code = 4017;
        $resp->hdie();
    }
    $conn->close();

    $params = $_GET;
    $serial = randomCard(32);

    $params["serial"] = $serial;

    $rs = sendTransferMsg($params);

    $resp->serial = $serial;
    if ($rs == "1") {
        $resp->ok = true;
        $resp->msg = "转账发起成功";

    }else{
        $resp->code = 4066;
        $resp->msg = "转账发起失败";
        $resp->cont =$rs;
        die($rs);
    }

    $resp->hecho();

}

function transferUpdate()
{

    $resp = new  BaseResp();

    $prefix = md5($_GET["org_id"]);
    $usercode = $_GET["usercode"];
    $pay_pwd = md5($_GET["pay_pwd"]);
    $coin_code = $_GET["coin_code"];
    $fcard = $_GET["card"];
    $tcard = $_GET["tcard"];
    $amount = $_GET["amount"];
    $backurl = $_GET["backurl"];
    $serial = $_GET["serial"];

    $conn = sqlconn();




    $conn->autocommit(false);

    $sql = "UPDATE `{$prefix}_card` SET balance=balance+{$amount} WHERE effe=TRUE AND  card='{$tcard}' ";
    $res0 = $conn->query($sql);

    $sql = "UPDATE `{$prefix}_card` SET balance=balance-{$amount} WHERE effe=TRUE AND  card='{$fcard}' ";
    $res1 = $conn->query($sql);

    $sql = "SELECT balance FROM `{$prefix}_card` WHERE effe=TRUE AND card='{$fcard}'   LIMIT 1";
    $res3 = $conn->query($sql);

    $fbalance = $res3->fetch_assoc()["balance"];

    $sql = "SELECT balance FROM `{$prefix}_card` WHERE effe=TRUE AND card='{$tcard}'   LIMIT 1";
    $res4 = $conn->query($sql);
    $tbalance = $res4->fetch_assoc()["balance"];

    $time = time();
    $sql = "INSERT INTO  `{$prefix}_bill` (fcard,tcard,fbalance,tbalance,amount,coin_code,serial,time) VALUES ('{$fcard}','{$tcard}',{$fbalance},{$tbalance},{$amount},'{$coin_code}','{$serial}',{$time})";
    $res2 = $conn->query($sql);

    if ($res0 && $res1 && $res2&& $res3 && $res4) {
        $conn->commit();
        $resp->msg = "转账成功";
        $resp->ok = true;
    } else {
        $conn->rollback();
        $jieguo0 = $res0 ? "1" : "0";
        $jieguo1 = $res1 ? "1" : "0";
        $jieguo2 = $res2 ? "1" : "0";
        $jieguo3 = $res3 ? "1" : "0";
        $jieguo4 = $res4 ? "1" : "0";

        $resp->msg = "转账失败-" . $jieguo0 . $jieguo1 . $jieguo2;
        $resp->code = 4018;
    }

    $conn->close();
    $resp->hecho();

    get($backurl . "?" . "serial=" . $serial . "&ok=" . $resp->ok);

}


function balance()
{
    $resp = new  BaseResp();
    if (empty($_GET["org_id"])) {
        $resp->msg = "请填写组织机构唯一标示org_id";
        $resp->code = 4019;
        $resp->hdie();
    }

    if (empty($_GET["usercode"])) {
        $resp->msg = "请填写usercode";
        $resp->code = 4020;
        $resp->hdie();
    }

    if (empty($_GET["card"])) {
        $resp->msg = "请填写card";
        $resp->code = 4022;
        $resp->hdie();
    }


    $conn = sqlconn();

    $prefix = md5($_GET["org_id"]);
    $usercode = $_GET["usercode"];
    $pay_pwd = md5($_GET["pay_pwd"]);
    $card = $_GET["card"];

    $sql = "SELECT balance,coin_code FROM `{$prefix}_card` WHERE effe=TRUE AND  usercode='{$usercode}' AND card='{$card}'  LIMIT 1";
    $res = $conn->query($sql);
    if ($res) {
        if ($res->num_rows == 0) {
            $resp->msg = "卡号不正确";
            $resp->code = 4023;
            $resp->hecho();
        } else {
            $resp->ok = true;
            $resp->info = $res->fetch_assoc();
            $resp->hecho();
            $res->free_result();
        }
    } else {
        $resp->msg = "目标card查询失败";
        $resp->code = 4024;
        $resp->hecho();
    }

    $conn->close();

}

function bills()
{
    $resp = new  BaseResp();

    if (empty($_GET["org_id"])) {
        $resp->msg = "请填写组织机构唯一标示org_id";
        $resp->code = 4025;
        $resp->hdie();
    }
    if (empty($_GET["usercode"])) {
        $resp->msg = "请填写usercode";
        $resp->code = 4026;
        $resp->hdie();
    }
    $card = null;
    if (!empty($_GET["card"])) {
        $card = $_GET["card"];

    }

    $max_id = null;
    if (!empty($_GET["max_id"])) {
        $card = $_GET["max_id"];

    }
    $since_id = null;
    if (!empty($_GET["since_id"])) {
        $card = $_GET["since_id"];

    }
    $count = 10;
    if (!empty($_GET["count"])) {
        $card = $_GET["count"];

    }


    $usercode = $_GET["usercode"];
    $prefix = md5($_GET["org_id"]);

    $sql = "SELECT b.* FROM `{$prefix}_card` c , `{$prefix}_bill` b WHERE c.effe=TRUE AND  c.usercode='{$usercode}' AND (c.card=b.tcard OR c.card=b.fcard) ";

    if ($card != null) {
        $sql .= " AND c.card = '{$card}'";
    }

    if ($max_id == null && $since_id == null) {

    } elseif ($max_id && $since_id == null) {
        $sql .= " AND b.id < " . $max_id;

    } elseif ($max_id == null && $since_id) {
        $sql .= " AND b.id > " . $since_id;

    } elseif ($max_id && $since_id) {
        if ($max_id <= $since_id) {
            $resp->msg = "max_id 应大于 since_id";
            $resp->code = 4027;
            $resp->hdie();
        }
        $sql .= " AND b.id > {$since_id} AND b.id < {$max_id}";

    }

    $sql .= " ORDER BY b.id DESC LIMIT " . $count;

    $conn = sqlconn();
    $res = $conn->query($sql);
    if ($res) {
        while ($dic = $res->fetch_assoc()) {
            array_push($resp->list, $dic);
        }
        $resp->ok = true;
        $resp->hecho();
        $res->free_result();
    } else {
        $resp->msg = "card查询失败";
        $resp->code = 4029;
        $resp->hecho();
    }

    $conn->close();
}

call_user_func(basename($_SERVER["PATH_INFO"]));
