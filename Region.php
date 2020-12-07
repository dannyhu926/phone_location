<?php

class Region
{
    /**
     * 是否为直辖市
     * @param string $name 名称
     * @return bool
     */
    public static function isMunicipality($name) {
        return in_array($name, [
            '北京', '上海', '天津', '重庆'
        ]);
    }

    /**
     * 是否为特别行政区
     * @param string $name 名称
     * @return bool
     */
    public static function isSAR($name) {
        return in_array($name, [
            '香港特别行政区', '澳门特别行政区'
        ]);
    }

    /**
     * 通过座机号码取区号.
     *
     * @param $mobile 号码
     *
     * @return bool|string
     */
    public function getAreaCodeByPhoneNumber($mobile)
    {
        $areaCode = '';
        $prefix = substr($mobile, 0, 2);
        if (in_array($prefix, ['01', '02'])) {
            //3位区号
            $areaCode = substr($mobile, 0, 3);
        } elseif (in_array($prefix, ['03', '04', '05', '06', '07', '08', '09'])) {
            //4位区号
            $areaCode = substr($mobile, 0, 4);
        }

        return $areaCode;
    }
    
    /**
     * 是否为自治区
     * @param string $name 名称
     * @return bool
     */
    public static function isAutonomousRegion($name) {
        return in_array($name, [
            '内蒙古自治区', '新疆维吾尔自治区', '广西壮族自治区', '宁夏回族自治区', '西藏自治区'
        ]);
    }

    /**
     * 是否为省份
     * @param string $name 名称
     * @return bool
     */
    public static function isProvince($name) {
        return !static::isSAR($name) && !static::isAutonomousRegion($name) && !static::isMunicipality($name);
    }

    function getRegions($type = 1, $parent_id = 1) {
        return $this->where(['region_level' => $type, 'parent_id' => $parent_id])->select();
    }
    /**
     * 省份下的城市
     */
    public function getCitysByProvinceId($pid) {
        $cityArray = array();
        if ($pid > 0) {
            $cityArray = $this->getRegions(2, $pid);
        }
        return $cityArray;
    }

    /**
     * 城市下的地区
     */
    public function getDistrictsByCityId($city_id) {
        $areaArray = array();
        if ($city_id > 0) {
            $areaArray = $this->getRegions(3, $city_id);;
        }
        return $areaArray;
    }

    /**
     * 获取省市区完整名称
     */
    public function getFullDistrict($province, $city, $area, $delimiter = '') {
        $regions = $this->getAllRegions();
        $provinceName = $cityName = $areaName = '';
        if ($province > 0 && array_key_exists($province, $regions)) {
            $provinceName = $regions[$province];
        }
        if ($city > 0 && array_key_exists($city, $regions)) {
            $cityName = str_replace(['县', '市辖区'], '', $regions[$city]);
        }
        if ($area > 0 && array_key_exists($area, $regions)) {
            $areaName = $regions[$area];
        }
        return $provinceName . $delimiter . $cityName . $delimiter . $areaName;
    }

    public function getAllRegions() {
        $result = array();
        $regions = $this->select();
        foreach ($regions as $r) {
            $result[$r['region_id']] = $r['region_name'];
        }
        return $result;
    }
} 