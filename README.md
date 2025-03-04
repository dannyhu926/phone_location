# phone_location.sql
手机号码归属地数据库，2025年03月 515858条数据  手机归属地查询，运营商，邮编，区号。

手机号可以携号转网归属运营商不一定准确

首先普及一下11位手机号的构成规则

前3位———网络识别号；

判断移动，联通，电信  select isp from phone_location where pref='130';

第4-7位———地区编码；

所以把你的号码前7位放到度娘搜索就知道哪个城市

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

#数据修复

SELECT count(DISTINCT city_code) aa, city from phone_location GROUP BY city HAVING aa>1

update phone_location set pref=left(phone,3) where pref is null;

SELECT CONCAT("update phone_location set post_code='",post_code,"', province='",province,"', city='",city,"', area_code='",area_code,"' where city_code='",city_code,"' and area_code is null;") from phone_location where city_code in(
SELECT DISTINCT city_code from phone_location where area_code is null
) and area_code is not null GROUP BY city_code

SELECT concat("update phone_location set isp='",isp,"',isp_type=",isp_type," where pref='",pref,"' and isp is null;") aaa from phone_location where isp is not null and pref in (SELECT DISTINCT pref from phone_location where isp is null) GROUP BY isp,pref 
