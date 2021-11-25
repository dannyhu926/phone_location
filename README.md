# phone_location.sql
手机号码归属地数据库，2021年482051条数据  手机归属地查询，运营商，邮编，区号。
手机号可以携号转网归属运营商不一定准确

数据库收录未知的号码，通过第三方网站补充完善手机号码归属地数据库

未知号码列表sql：select count(1) from phone_location where isp is null

数据库收录未知的号码：

1）通过第三方网站补充完善手机号码归属地数据库

2）第三方接口查询

https://www.qqzeng.com/phone/

how 2 use:

select * from phone_location where phone like "1337785%" limit 1;

#region.sql
行政区划数据库_with+经纬度-省市区-邮编-区号-拼音-简称 3750条数据

select city_code select * from region where LENGTH(city_code)>2 and level>1

报警电话110 电信

电信客服 10000  

联通客服 10010

移动  10086


