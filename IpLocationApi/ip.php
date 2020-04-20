<?php
error_reporting(0);

include "./libs/iplocation.class.php";
include "./libs/json.fun.php";

//======================demo==========================
$demo = $_GET[demo];

$help = '使用之前请去网上下载纯真IP数据库, "QQWry.dat" 放到当前目录下即可，有更多建议请联系 QQ:85431993<br>E-mail:threesky@gmail.com<br>QQ群:6361203';

$api = "本站接口:<br>http://api.heqee.com/ip/?ip=IP地址&format=返回格式(text,js,json)&charset=返回编码(utf8,gbk)";

if($demo!=""){
  if(!in_array($demo,array("code","help","api","qqwry"))){error("参数错误!");}
  switch($demo){
	case "code":
		exit(highlight_file("demo.php",TRUE));
	break;
	case "help":
		exit($help);
	break;
	case "api":
		exit($api);
	break;
	case "qqwry":
		if (!file_exists("QQWry.Dat")){
			echo "QQWry.Dat不存在，请去网上下载纯真IP数据库, ‘QQWry.dat' 放到当前目录下";exit;
		}

	break;
  }
}
//===================================================



#需要查询的IP
$ip = $_GET[ip];

//返回格式
$format = $_GET[format];//默认text,json,js
//返回编码
$charset = $_GET[charset]; //默认utf-8,gbk或gb2312


#实例化(必须)
$ip_l=new ipLocation();

//======demo==========
if($_GET[getip]){
	$ip = $ip_l->getIP();
	$format ="json";
	$charset = "utf8";
}
//====================

$address=$ip_l->getaddress($ip);



$add =heqeeip($ip,$address,$charset,$format);

$ip_l=NULL;

echo $add;





function heqeeip($ip,$address,$charset="utf8",$format="text"){
	if(@in_array($charset,array("utf8","utf-8","UTF8","UTF-8"))||$charset==""){
	@header("Content-Type: text/html; charset=utf-8");
	$address["area1"] = iconv('GB2312','utf-8',$address["area1"]);
	$address["area2"] = iconv('GB2312','utf-8',$address["area2"]);
	$add=$address["area1"]." ".$address["area2"];
	}elseif(@in_array($charset,array("gbk","gb2312","GB2312","GB-2312"))){
		@header("Content-Type: text/html; charset=gb2312");
		$add=$address["area1"]." ".$address["area2"];
	}else{
	//
	}

	switch($format){
		case "text":
			$add = $add;
		break;
		case "js":
			$add="document.write('".$add."');";
		break;
		case "json":
			$a = array("ip"=>$ip,"iplocation"=>$add);
			$add = "(".JSON($a).")";
		break;
		default:
			$add = $add;
	  }
	  return $add;
}

?>