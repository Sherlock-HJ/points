<?php
/**
 * Created by PhpStorm.
 * User: 吴宏佳
 * Date: 2018/8/23
 * Time: 下午1:50
 */

require_once BASEPATH . "/lib/sqlconn.php";

function randomStr($len)
{
    $pattern = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ+()[]' . time();
    $res = "";
    for ($num = 0; $num < $len; $num++) {
        $res = $res . $pattern[rand(0, strlen($pattern) - 1)];
    }
    return $res;
}

function add()
{
    $resp = new  BaseResp();

    if (empty($_GET["name"])) {
        $resp->msg = "未填写组织机构名";
        $resp->code = 2011;
        $resp->hdie();
    }

    $conn = sqlconn();

    $sql = "CREATE TABLE IF NOT EXISTS `organization` ( 
            `id` INT NOT NULL AUTO_INCREMENT , 
            `name` TINYTEXT NOT NULL , 
            `org_id` TINYTEXT NOT NULL , 
            `org_secrt` TINYTEXT NOT NULL , 
            `pub_key_path` TINYTEXT NOT NULL COMMENT 'RSA公钥文件路径' , 
            PRIMARY KEY (`id`)
            ) ENGINE = InnoDB COMMENT = '组织机构表'";
    $res = $conn->query($sql);
    if (!$res) {
        $resp->msg = "组织机构表创建失败";
        $resp->code = 2000;
        $resp->hdie();
    }

    $name = $_GET["name"];
    $org_id = randomStr(16);
    $org_secrt = randomStr(32);
    $pub_key_path = "";
    $prefix = md5($org_id);

    $sql = "SELECT id FROM organization WHERE name='{$name}' OR org_secrt='{$org_secrt}' OR org_id='{$org_id}' LIMIT 1";
    $res0 = $conn->query($sql);
    if (!$res0) {
        $resp->msg = "组织机构查新失败";
        $resp->code = 2001;
        $resp->hdie();
    }

    if ($res0->num_rows > 0) {
        $res0->free_result();

        $resp->msg = "组织机构名称重复";
        $resp->code = 2002;
        $resp->hdie();

    }


    $sql = "INSERT INTO organization (name,org_id,org_secrt,pub_key_path) VALUES ('{$name}','{$org_id}','{$org_secrt}','{$pub_key_path}')";
    $res1 = $conn->query($sql);
    $insertID = $conn->insert_id;
    if ($res1) {


        $sql = "CREATE TABLE IF NOT EXISTS  `{$prefix}_coin` ( 
            `id` INT NOT NULL AUTO_INCREMENT , 
            `name` TINYTEXT NOT NULL , 
            `code` TINYTEXT NOT NULL , 
            PRIMARY KEY (`id`)
            ) ENGINE = InnoDB COMMENT = '{$name} 币表'";
        $res3 = $conn->query($sql);
        if (!$res3) {
            $sql = "DELETE FROM organization WHERE id={$insertID}";
            $res1 = $conn->query($sql);
            $resp->msg = "组织机构创建失败";
            $resp->code = 2003;
            $resp->hdie();
        }

        $sql = "CREATE TABLE IF NOT EXISTS  `{$prefix}_card` ( 
            `id` INT NOT NULL AUTO_INCREMENT , 
            `usercode` TINYTEXT NOT NULL , 
            `pay_pwd` TINYTEXT NOT NULL , 
            `card` TINYTEXT NOT NULL , 
            `balance` INT NOT NULL , 
            `effe` BOOLEAN NOT NULL , 
            `coin_code` TINYTEXT NOT NULL , 
            PRIMARY KEY (`id`)
            ) ENGINE = InnoDB COMMENT = '{$name} 币账户表'";
        $res4 = $conn->query($sql);
        if (!$res4) {
            $sql = "DROP TABLE `{$prefix}_coin`";
            $res5 = $conn->query($sql);
            $sql = "DELETE FROM organization WHERE id={$insertID}";
            $res1 = $conn->query($sql);
            $resp->msg = "组织机构创建失败";
            $resp->code = 2003;
            $resp->hdie();
        }

        $sql = "CREATE TABLE IF NOT EXISTS  `{$prefix}_bill` ( 
            `id` INT NOT NULL AUTO_INCREMENT , 
            `fcard` TINYTEXT NOT NULL , 
            `tcard` TINYTEXT NOT NULL , 
            `amount` INT NOT NULL , 
            `coin_code` TINYTEXT NOT NULL , 
            `time` INT NOT NULL , 
            PRIMARY KEY (`id`)
            ) ENGINE = InnoDB COMMENT = '{$name} 币明细表'";
        $res5 = $conn->query($sql);
        if (!$res5) {
            $sql = "DROP  TABLE `{$prefix}_card`";
            $res5 = $conn->query($sql);
            $sql = "DROP TABLE `{$prefix}_coin`";
            $res5 = $conn->query($sql);
            $sql = "DELETE FROM organization WHERE id={$insertID}";
            $res1 = $conn->query($sql);
            $resp->msg = "组织机构创建失败";
            $resp->code = 2003;
            $resp->hdie();
        }


        $resp->ok = true;
        $resp->msg = "组织机构创建成功";
        $resp->info = array("org_id" => $org_id, "org_secrt" => $org_secrt);
        $resp->hecho();


    } else {
        $resp->msg = "组织机构创建失败";
        $resp->code = 2004;
        $resp->hdie();
    }
    $conn->close();


}
function olist(){
    $resp = new  BaseResp();

    $page = 1;
    if (!empty($_GET["page"])) {
        $page = $_GET["page"];

    }

    $count = 10;
    if (!empty($_GET["count"])) {
        $count = $_GET["count"];

    }

    $conn = sqlconn();

    $sql = "SELECT * FROM `organization` ORDER BY id DESC LIMIT ". ($page - 1) * $count . "," . $count;
    $res = $conn->query($sql);
    if ($res) {
        while ($dic = $res->fetch_assoc()) {
            array_push($resp->list, $dic);
        }
        $resp->ok = true;
        $res->free_result();

    } else {
        $resp->msg = "查询失败";
        $resp->code = 2024;
    }

    $sql = "SELECT COUNT(id) AS total FROM `organization` ";
    $res = $conn->query($sql);
    $resp->total = $res->fetch_assoc()["total"];

    $resp->hecho();

    $conn->close();
}
function add_coin()
{
    $resp = new  BaseResp();

    if (empty($_GET["name"])) {
        $resp->msg = "未填写币名";
        $resp->code = 2005;
        $resp->hdie();
    }
    if (empty($_GET["org_id"])) {
        $resp->msg = "请填写组织机构唯一标示org_id";
        $resp->code = 2006;
        $resp->hdie();
    }
    if (empty($_GET["code"])) {
        $resp->msg = "未填写币code";
        $resp->code = 2007;
        $resp->hdie();
    }

    $name = $_GET["name"];
    $org_id = $_GET["org_id"];
    $code = $_GET["code"];

    $conn = sqlconn();


    $prefix = md5($org_id);

    $sql = "SELECT id FROM `{$prefix}_coin` WHERE name='{$name}' OR code='{$code}'  LIMIT 1";
    $res = $conn->query($sql);
    if (!$res) {
        $resp->msg = "币查询失败" . $conn->error;
        $resp->code = 2008;
        $resp->hdie();
    }
    if ($res->num_rows > 0) {
        $res->free_result();

        $resp->msg = "币名 或者 code重复";
        $resp->code = 2009;
        $resp->hdie();
    }

    $sql = "INSERT INTO `{$prefix}_coin` (`name`,code) VALUES ('{$name}','{$code}')";
    $res = $conn->query($sql);
    if ($res) {
        $resp->ok = true;
        $resp->msg = "成功创建币";
        $resp->hecho();
    } else {
        $resp->msg = "币创建失败" . $conn->error;
        $resp->code = 2010;
        $resp->hecho();
    }

    $conn->close();

}
function coin_list() {
    $resp = new  BaseResp();

    if (empty($_GET["org_id"])) {
        $resp->msg = "请填写组织机构唯一标示org_id";
        $resp->code = 2016;
        $resp->hdie();
    }

    $page = 1;
    if (!empty($_GET["page"])) {
        $page = $_GET["page"];

    }

    $count = 10;
    if (!empty($_GET["count"])) {
        $count = $_GET["count"];

    }
    $org_id = $_GET["org_id"];

    $conn = sqlconn();
    $prefix = md5($org_id);

    $sql = "SELECT * FROM `{$prefix}_coin` ORDER BY id DESC LIMIT ". ($page - 1) * $count . "," . $count;
    $res = $conn->query($sql);
    if ($res) {
        while ($dic = $res->fetch_assoc()) {
            array_push($resp->list, $dic);
        }
        $resp->ok = true;
        $res->free_result();

    } else {
        $resp->msg = "查询失败";
        $resp->code = 2034;
    }

    $sql = "SELECT COUNT(id) AS total FROM `{$prefix}_coin` ";
    $res = $conn->query($sql);
    $resp->total = $res->fetch_assoc()["total"];

    $resp->hecho();

    $conn->close();
}

call_user_func(basename($_SERVER["PATH_INFO"]));
