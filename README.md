# phone_location
手机号码归属地数据库，2018年5月版 397506条  手机归属地查询，运营商，邮编，区号

how 2 use:

select * from phone_location where phone like "1337785%" limit 1;


$chunkSize = 200;
$startRow = 2; //从第二行开始读取
while (true) {
    $data = $this->readFilterData($startRow, $chunkSize);
    if(empty($data)){
        break;
    }
    //todo logic here
    $startRow = $startRow + $chunkSize + 1;
}

