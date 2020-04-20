<?php
function php_json_encode($arr)
{
    $json_str = "";
    if(is_array($arr))
    {
      $pure_array = true;
      $array_length = count($arr);
      for($i=0;$i<$array_length;$i++)
      {
        if(! isset($arr[$i]))
        {
          $pure_array = false;
          break;
        }
      }
      if($pure_array)
      {
        $json_str ="[";
        $temp = array();
        for($i=0;$i<$array_length;$i++)      
        {
          $temp[] = sprintf("%s", php_json_encode($arr[$i]));
        }
        $json_str .= implode(",",$temp);
        $json_str .="]";
      }
      else
      {
        $json_str ="{";
        $temp = array();
        foreach($arr as $key => $value)
        {
          $temp[] = sprintf("\"%s\":%s", $key, php_json_encode($value));
        }
        $json_str .= implode(",",$temp);
        $json_str .="}";
      }
    }
    else
    {
      if(is_string($arr))
      {
        $json_str = "\"". json_encode_string($arr) . "\"";
      }
      else if(is_numeric($arr))
      {
        $json_str = $arr;
      }
      else
      {
        $json_str = "\"". json_encode_string($arr) . "\"";
      }
    }
    return $json_str;
}

function json_encode_string($in_str) {
    mb_internal_encoding("UTF-8");
    $convmap = array(0x80, 0xFFFF, 0, 0xFFFF);
    $str = "";
    for ($i = mb_strlen($in_str)-1; $i>=0; $i--) {
        $mb_char = mb_substr($in_str, $i, 1);
        if (mb_ereg("&#(\\d+);", mb_encode_numericentity($mb_char, $convmap, "UTF-8"), $match)) {
            $str = sprintf("\\u%04x", $match[1]) . $str;
        } else {
            $str = $mb_char . $str;
        }
    }
    return $str;
}

/**************************************************************
 *
 *	使用特定function对数组中所有元素做处理
 *	@param	string	&$array		要处理的字符串
 *	@param	string	$function	要执行的函数
 *	@return boolean	$apply_to_keys_also		是否也应用到key上
 *	@access public
 *
 *************************************************************/
function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
{
    static $recursive_counter = 0;
    if (++$recursive_counter > 1000) {
        die('possible deep recursion attack');
    }
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            arrayRecursive($array[$key], $function, $apply_to_keys_also);
        } else {
            $array[$key] = $function($value);
        }
 
        if ($apply_to_keys_also && is_string($key)) {
            $new_key = $function($key);
            if ($new_key != $key) {
                $array[$new_key] = $array[$key];
                unset($array[$key]);
            }
        }
    }
    $recursive_counter--;
}
 
/**************************************************************
 *
 *	将数组转换为JSON字符串（兼容中文）
 *	@param	array	$array		要转换的数组
 *	@return string		转换得到的json字符串
 *	@access public
 *
 *************************************************************/
function JSON($array) {
	arrayRecursive($array, 'urlencode', true);
	if (function_exists('json_encode')) {
		$json = json_encode($array);
	} else {
		$json = php_json_encode($array);
	}
	return urldecode($json);
}


//demo
//echo JSON(array('JSON字符串'));


?>
