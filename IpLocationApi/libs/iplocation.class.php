<?php
//*
//文件头 [第一条索引的偏移量 (4byte)] + [最后一条索引的偏移地址 (4byte)]     8字节
//记录区 [结束ip (4byte)] + [地区1] + [地区2]                                4字节+不定长
//索引区 [开始ip (4byte)] + [指向记录区的偏移地址 (3byte)]                   7字节
//注意:使用之前请去网上下载纯真IP数据库,并改名为 "CoralWry.dat" 放到当前目录下即可.
//by 查询吧 www.query8.com
//*

class IpLocation {
var $fp;
var $firstip;  //第一条ip索引的偏移地址
var $lastip;   //最后一条ip索引的偏移地址
var $totalip;  //总ip数
//*
//构造函数,初始化一些变量
//$datfile 的值为纯真IP数据库的名子,可自行修改.
//*
function ipLocation($datfile = "QQWry.Dat"){
  $this->fp=fopen($datfile,'rb')or die("QQWry.Dat不存在，请去网上下载纯真IP数据库, 'QQWry.dat' 放到当前目录下");   //二制方式打开
  $this->firstip = $this->get4b(); //第一条ip索引的绝对偏移地址
  $this->lastip = $this->get4b();  //最后一条ip索引的绝对偏移地址
  $this->totalip =($this->lastip - $this->firstip)/7 ; //ip总数 索引区是定长的7个字节,在此要除以7,
  register_shutdown_function(array($this,"closefp"));  //为了兼容php5以下版本,本类没有用析构函数,自动关闭ip库.
}
//*
//关闭ip库
//*
function closefp(){
fclose($this->fp);
}
//*
//读取4个字节并将解压成long的长模式
//*
function get4b(){
  $str=unpack("V",fread($this->fp,4));
  return $str[1];
}
//*
//读取重定向了的偏移地址
//*
function getoffset(){
  $str=unpack("V",fread($this->fp,3).chr(0));
  return $str[1];
}
//*
//读取ip的详细地址信息
//*
function getstr(){
  $split=fread($this->fp,1);
  while (ord($split)!=0) {
    $str .=$split;
	$split=fread($this->fp,1);
  }
  return $str;
}
//*
//将ip通过ip2long转成ipv4的互联网地址,再将他压缩成big-endian字节序
//用来和索引区内的ip地址做比较
//*
function iptoint($ip){
  return pack("N",intval(ip2long($ip)));
}
//*
//获取客户端ip地址
//注意:如果你想要把ip记录到服务器上,请在写库时先检查一下ip的数据是否安全.
//*
function getIP() {
        if (getenv('HTTP_CLIENT_IP')) {
				$ip = getenv('HTTP_CLIENT_IP'); 
		}
		elseif (getenv('HTTP_X_FORWARDED_FOR')) { //获取客户端用代理服务器访问时的真实ip 地址
				$ip = getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif (getenv('HTTP_X_FORWARDED')) { 
				$ip = getenv('HTTP_X_FORWARDED');
		}
		elseif (getenv('HTTP_FORWARDED_FOR')) {
				$ip = getenv('HTTP_FORWARDED_FOR'); 
		}
		elseif (getenv('HTTP_FORWARDED')) {
				$ip = getenv('HTTP_FORWARDED');
		}
		else { 
				$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
}
//*
//获取地址信息
//*
function readaddress(){
  $now_offset=ftell($this->fp); //得到当前的指针位址
  $flag=$this->getflag();
  switch (ord($flag)){
         case 0:
		     $address="";
		 break;
		 case 1:
		 case 2:
		     fseek($this->fp,$this->getoffset());
			 $address=$this->getstr();
		 break;
		 default:
		     fseek($this->fp,$now_offset);
		     $address=$this->getstr();
		 break;
  }
  return $address;
}
//*
//获取标志1或2
//用来确定地址是否重定向了.
//*
function getflag(){
  return fread($this->fp,1);
}
//*
//用二分查找法在索引区内搜索ip
//*
function searchip($ip){
  $ip=gethostbyname($ip);     //将域名转成ip
  $ip_offset["ip"]=$ip;
  $ip=$this->iptoint($ip);    //将ip转换成长整型
  $firstip=0;                 //搜索的上边界
  $lastip=$this->totalip;     //搜索的下边界
  $ipoffset=$this->lastip;    //初始化为最后一条ip地址的偏移地址
  while ($firstip <= $lastip){
    $i=floor(($firstip + $lastip) / 2);          //计算近似中间记录 floor函数记算给定浮点数小的最大整数,说白了就是四舍五也舍
	fseek($this->fp,$this->firstip + $i * 7);    //定位指针到中间记录
	$startip=strrev(fread($this->fp,4));         //读取当前索引区内的开始ip地址,并将其little-endian的字节序转换成big-endian的字节序
	if ($ip < $startip) {
	   $lastip=$i - 1;
	}
	else {
	   fseek($this->fp,$this->getoffset());
	   $endip=strrev(fread($this->fp,4));
	   if ($ip > $endip){
	      $firstip=$i + 1;
	   }
	   else {
	      $ip_offset["offset"]=$this->firstip + $i * 7;
	      break;
	   }
	}
  }
  return $ip_offset;
}
//*
//获取ip地址详细信息
//*
function getaddress($ip){
  $ip_offset=$this->searchip($ip);  //获取ip 在索引区内的绝对编移地址
  $ipoffset=$ip_offset["offset"];
  $address["ip"]=$ip_offset["ip"];
  fseek($this->fp,$ipoffset);      //定位到索引区
  $address["startip"]=long2ip($this->get4b()); //索引区内的开始ip 地址
  $address_offset=$this->getoffset();            //获取索引区内ip在ip记录区内的偏移地址
  fseek($this->fp,$address_offset);            //定位到记录区内
  $address["endip"]=long2ip($this->get4b());   //记录区内的结束ip 地址
  $flag=$this->getflag();                      //读取标志字节
  switch (ord($flag)) {
         case 1:  //地区1地区2都重定向
		 $address_offset=$this->getoffset();   //读取重定向地址
		 fseek($this->fp,$address_offset);     //定位指针到重定向的地址
		 $flag=$this->getflag();               //读取标志字节
		 switch (ord($flag)) {
		        case 2:  //地区1又一次重定向,
				fseek($this->fp,$this->getoffset());
				$address["area1"]=$this->getstr();
				fseek($this->fp,$address_offset+4);      //跳4个字节
				$address["area2"]=$this->readaddress();  //地区2有可能重定向,有可能没有
				break;
				default: //地区1,地区2都没有重定向
				fseek($this->fp,$address_offset);        //定位指针到重定向的地址
				$address["area1"]=$this->getstr();
				$address["area2"]=$this->readaddress();
				break;
		 }
		 break;
		 case 2: //地区1重定向 地区2没有重定向
		 $address1_offset=$this->getoffset();   //读取重定向地址
		 fseek($this->fp,$address1_offset);  
		 $address["area1"]=$this->getstr();
		 fseek($this->fp,$address_offset+8);
		 $address["area2"]=$this->readaddress();
		 break;
		 default: //地区1地区2都没有重定向
		 fseek($this->fp,$address_offset+4);
		 $address["area1"]=$this->getstr();
		 $address["area2"]=$this->readaddress();
		 break;
  }
  //*过滤一些无用数据
  if (strpos($address["area1"],"CZ88.NET")!=false){
      $address["area1"]="未知";
  }
  if (strpos($address["area2"],"CZ88.NET")!=false){
      $address["area2"]=" ";
  }
  return $address;
 }

} 
//*ipLocation class end

?>