<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-type:text/json;charset=utf-8");
$host     = "localhost";
$user     = "root";
$pass     = "baoqingxin2016";
$database = "back_stage";
$con      = mysqli_connect($host, $user, $pass, $database);

if (!$con) {
    die('Could not connect: ' . mysql_error());
}
//设置编码方式utf-8
mysqli_query($con, "set names utf8");
?>