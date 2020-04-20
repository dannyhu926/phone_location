<?php
/*
IpLocation API
使用之前请去网上下载纯真IP数据库, "QQWry.dat" 放到当前目录下即可
*/
include "libs/iplocation.class.php";
#需要查询的IP
$ip = "221.231.1.1";
//返回格式
$format = "text";//默认text,json,xml,js
//返回编码
$charset = "utf8"; //默认utf-8,gbk或gb2312
#实例化(必须)
$ip_l=new ipLocation();
$address=$ip_l->getaddress($ip);

$address["area1"] = iconv('GB2312','utf-8',$address["area1"]);
$address["area2"] = iconv('GB2312','utf-8',$address["area2"]);

$add=$address["area1"]." ".$address["area2"];

echo $add;
?>