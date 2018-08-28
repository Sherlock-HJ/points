var express = require('express');
var http = require('http');
var app = express();
var bodyParser = require('body-parser');

var router = express.Router();


// 创建 application/x-www-form-urlencoded 编码解析
var urlencodedParser = bodyParser.text();


var taskDic = {};
// var curt_taskObj = null;

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

        taskObj.curtTaskObj.task(taskObj.curtTaskObj.body);

    }


}

router.post('/', urlencodedParser, function (req, res) {


    res.send("1");

    var obj = {};
    obj.body = req.body;
    obj.task = function (taskBody) {


        http.get(taskBody, function (res1) {


            var resData = "";
            res1.on("data", function (data) {
                resData += data;
            });
            res1.on("end", function () {
                console.log(new Date() + " -- " + resData);


                var key1 = taskBody.split("?")[0];

                var taskObj = arrayForKey(key1);
                taskObj.curtTaskObj = null;
                dealRequest(key1);

            });

        })

    }

    var key = req.body.split("?")[0];

    var taskObj = arrayForKey(key);
    taskObj.taskArr.push(obj);

    dealRequest(key);
    console.log("start");
    console.log(req.body);


});


module.exports = router;
