<?php
/**
 * https://github.com/jonnywang/phone PHP手机归属地扩展
 *
 * 0 省份 1城市 2邮编 3区号 4号码类型（1移动 2联通 3电信 4电信虚拟运营商 5联通虚拟运营商 6移动虚拟运营商）
 */
$phone = '1367152';
$sql = "select phone from phone_location where phone='$phone'";
$info = $this->conn->fetchRow($sql);
if (empty($info)) {
    $info = phone($phone);//需要按照扩展
    if ($info) {
        $pref = substr($phone, 0, 3);
        $sql = "insert into phone_location(pref,phone,province,city,isp,post_code,city_code) values(%s,%s,%s,%s,%s,%s,%s)";
        if (1 == $info['4']) {
            $isp = '移动';
        }
        if (2 == $info['4']) {
            $isp = '联通';
        }
        if (3 == $info['4']) {
            $isp = '电信';
        }
        if (4 == $info['4']) {
            $isp = '电信/虚拟';
        }
        if (5 == $info['4']) {
            $isp = '联通/虚拟';
        }
        if (6 == $info['4']) {
            $isp = '移动/虚拟';
        }
        $sql = sprintf($sql, $pref, $phone, $info['0'], $info['1'], $isp, $info['2'], $info['3']);
        $this->conn->exec($sql);
    }
}
var_dump($info);
