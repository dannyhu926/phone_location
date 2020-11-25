<?php
/**
 * https://github.com/jonnywang/phone PHP手机归属地扩展
 *
 * 0 省份 1城市 2邮编 3区号 4号码类型（1移动 2联通 3电信 4电信虚拟运营商 5联通虚拟运营商 6移动虚拟运营商）
 */
$phone = '1367152';

require __DIR__.'/phone_location/vendor/autoload.php';

use QL\QueryList;

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
        $result = ip138_location($phone);
        if (empty($result)) {
            $sql = "insert into not_found_number(phone,num) values($phone,1)";
            $this->conn->exec($sql);
        }
    }
}

function ip138_location($num)
{
    $result = false;
    $url = 'https://www.ip138.com/mobile.asp?mobile='.$num.'&action=mobile';
    $data = QueryList::get($url)->rules([  //设置采集规则
        // 采集所有a标签的文本内容
        'code' => ['td:eq(8)', 'text'],
        'city' => ['td:eq(4)', 'text'],
        'postCode' => ['td:eq(10)', 'text'],
        'cardType' => ['td:eq(6)', 'text'],
    ])->query()->getData();;
    $list = $data->all();
    extract($list);
    if (!empty($code) && !empty($postCode)) {
        //将结果保存到数据库中
        $sql = "INSERT INTO phone_location(num, city, cardtype, code, post_code) VALUES ($num, $city, $cardType, $code, $postCode)";
        $this->conn->exec($sql);

        $sql = "delete from not_found_number where phone='$num' limit 1";
        $this->conn->exec($sql);
        $result = true;
    }

    return $result;
}
