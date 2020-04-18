<?php
/**
 * 示例中sign会不定期调整
 * https://www.nowapi.com/api/phone.get
 * {
 * "success":"1",
 * "result":{
 * "status":"ALREADY_ATT",
 * "phone":"13800138000", //查询的手机号
 * "area":"010", //区号
 * "postno":"100000", //邮编
 * "att":"中国,北京", //归属地样式1
 * "ctype":"北京移动全球通卡", //卡类型
 * "par":"1380013", //手机号前缀
 * "prefix":"138", //号段
 * "operators":"移动", //所属运营商
 * "style_simcall":"中国,北京", //归属地样式1
 * "style_citynm":"中华人民共和国,北京市" //归属地样式2
 * }
 * }
 */
$demoKey = '10003';
$demoSign = 'b59bc3ef6191eb9f747dd4e83c99f2a4';
do {
    $sql = "select phone from phone_location where isp is not null and city_code is null and flag=0 limit 100";
    $list = $this->conn->fetchAll($sql);
    foreach ($list as $info) {
        $phonePref = $info['phone'];
        $url = sprintf(
            "http://api.k780.com/?app=phone.get&phone=%d1111&appkey=%s&sign=%s&format=json",
            $phonePref,
            $demoKey,
            $demoSign
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $result = curl_exec($ch);
        $arr = json_decode($result, true);
        if ($arr['success']) {
            $rs = $arr['result'];
            if ($rs['status'] == 'ALREADY_ATT') {
                $cityArr = explode(',', $rs['style_simcall']);
                $sql = "update phone_location set city_code='{$rs['area']}',isp='{$rs['operators']}', post_code='{$rs['postno']}',province='{$cityArr['1']}',city='{$cityArr['2']}',pref='{$rs['prefix']}', flag=1 where phone='$phonePref'";
                $output->writeln($sql);
                $this->conn->exec($sql);
            } else {
                $sql = "update phone_location set flag=1 where phone='$phonePref'";
                $output->writeln($sql);
                $this->conn->exec($sql);
            }
        }
    }
} while (!empty($list));

