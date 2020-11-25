<?php
/**
 * https://github.com/jonnywang/phone PHP手机归属地扩展
 *
 * 0 省份 1城市 2邮编 3区号 4号码类型（1移动 2联通 3电信 4电信虚拟运营商 5联通虚拟运营商 6移动虚拟运营商）
 */
require __DIR__.'/phone_location/vendor/autoload.php';

use QL\QueryList;


$phone = '1300020';
//$result = ip138_mobile_location_api($phone);
//var_dump(11);die;
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
        $sql = "delete from not_found_number where phone='$phone' limit 1";
        $this->conn->exec($sql);
    } else {
        $result = ip138_mobile_location_api($phone);
        if (empty($result)) {
            $sql = "insert into not_found_number(phone,num) values($phone,1)";
            $this->conn->exec($sql);
        }
    }
}

function ip138_mobile_location_api($num)
{
    $result = false;
    $url = 'https://www.ip138.com/mobile.asp?mobile='.$num.'&action=mobile';
    $data = QueryList::get($url)->rules([  //设置采集规则
        'city' => ['td:eq(4)', 'text'],
        'cardType' => ['td:eq(6)', 'text'],
        'cityCode' => ['td:eq(8)', 'text'],
        'postCode' => ['td:eq(10)', 'text'],
    ])->query()->getData();
    $list = $data->all();
    extract($list);

    $city = trim(str_replace(" ", " ", $city));
    $area = array_filter(explode(" ", $city));
    if (count($area) == 2) {
        var_dump($area);
        die;
        $province = $area['0'];
        $city = $area['1'];
    } else {
        $province = $city;
    }
    if (!empty($cityCode) && !empty($postCode)) {
        //将结果保存到数据库中
        $sql = "INSERT INTO phone_location(phone,province,city,isp,city_code,post_code) VALUES ('$num', '$province', '$city', '$cardType', '$cityCode', '$postCode')";
        $this->conn->exec($sql);

        $sql = "delete from not_found_number where phone='$phone' limit 1";
        $this->conn->exec($sql);
        $result = true;
    }

    return $result;
}