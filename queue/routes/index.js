var express = require('express');
var http = require('http');
var app = express();
var bodyParser = require('body-parser');
var mysql = require('mysql');

var router = express.Router();


// 创建 application/x-www-form-urlencoded 编码解析
var urlencodedParser = bodyParser.text();


var taskDic = {};
// var curt_taskObj = null;
var dbObj = {
    host: 'localhost',
    user: 'root',
    password: 'qwertyui',
    database: 'points'
};


function cacheCreateTable() {

    var connection = mysql.createConnection(dbObj);

    connection.connect();
    var  sql = "CREATE TABLE IF NOT EXISTS  `queue` (" +
        " `id` INT NOT NULL AUTO_INCREMENT ," +
        "`body` TEXT NOT NULL ," +
        "PRIMARY KEY (`id`)" +
        ") ENGINE = InnoDB COMMENT = '队列缓存表'";
    connection.query(sql,  function (err, result) {
        console.log(err);
        console.log(result);


    });




    var addSql = 'SELECT * FROM queue ';

    connection.query(addSql, function (err, result) {
        if (err) {
            console.log('[SELECT ERROR] - ', err.message);
            return;
        }

        console.log('--------------------------SELECT----------------------------');

        console.log('-----------------------------------------------------------------\n\n');

        for(var num = 0 ; num < result.length; num++){
            var obj = result[num];
            sendMsg(obj.body,obj.id);
        }

    });

    connection.end();

}

function cacheAdd(body) {
    var connection = mysql.createConnection(dbObj);

    connection.connect();

    var addSql = 'INSERT INTO queue (body) VALUES(?)';
    var addSqlParams = [body];

    connection.query(addSql, addSqlParams, function (err, result) {
        if (err) {
            console.log('[INSERT ERROR] - ', err.message);
            return;
        }

        console.log('--------------------------INSERT----------------------------');
        //console.log('INSERT ID:',result.insertId);
        console.log('INSERT ID:', result.insertId);
        console.log('-----------------------------------------------------------------\n\n');

        sendMsg(body,result.insertId);
        connection.end();

    });

}

function cachedDelete(ID) {
    var connection = mysql.createConnection(dbObj);

    connection.connect();

    var addSql = 'DELETE FROM queue WHERE id='+ID;

    connection.query(addSql, function (err, result) {
        if (err) {
            console.log('[DELETE ERROR] - ', err.message);
            return;
        }

        console.log('--------------------------DELETE----------------------------');
        //console.log('INSERT ID:',result.insertId);
        console.log('DELETE ID:', result);
        console.log('-----------------------------------------------------------------\n\n');
        connection.end();

    });

}

function arrayForKey(key) {

    if (taskDic[key] === undefined) {
        taskDic[key] = {curtTaskObj: null, taskArr: []};
    }
    return taskDic[key];
}

function dealRequest(key) {
    var taskObj = arrayForKey(key);


    if (taskObj.curtTaskObj !== null || taskObj.taskArr.length <= 0) {

        return null;
    } else {

        taskObj.curtTaskObj = taskObj.taskArr.shift();

        taskObj.curtTaskObj.task(taskObj.curtTaskObj.body,taskObj.curtTaskObj.ID);

    }


}


function sendMsg(body,ID) {
    var obj = {};
    obj.body = body;
    obj.ID = ID;
    obj.task = function (taskBody,ID1) {


        http.get(taskBody, function (res1) {


            var resData = "";
            res1.on("data", function (data) {
                resData += data;
            });
            res1.on("end", function () {
                console.log(new Date() + " -- " + resData);

                cachedDelete(ID1);
                var key1 = taskBody.split("?")[0];

                var taskObj = arrayForKey(key1);
                taskObj.curtTaskObj = null;
                dealRequest(key1);

            });

        })

    };

    var key = body.split("?")[0];

    var taskObj = arrayForKey(key);
    taskObj.taskArr.push(obj);

    dealRequest(key);
    console.log("start");
    console.log(body);

}

router.post('/', urlencodedParser, function (req, res) {

    cacheAdd(req.body);
    res.send("1");


});


cacheCreateTable();


module.exports = router;
