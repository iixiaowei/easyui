<?php

/***
 * 函数库
 * @author jsyzchenchen@gmail.com
 * date 2015-11-30
 */

/**
 * +----------------------------------------------------------
 * 字符串截取，支持中文和其他编码
 * +----------------------------------------------------------
 *
 * @static
 *
 * @access public
 *         +----------------------------------------------------------
 * @param string $str
 *            需要转换的字符串
 * @param string $start
 *            开始位置
 * @param string $length
 *            截取长度
 * @param string $charset
 *            编码格式
 * @param string $suffix
 *            截断显示字符
 *            +----------------------------------------------------------
 * @return string +----------------------------------------------------------
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr")) {
        if ($suffix)
            return mb_substr($str, $start, $length, $charset) . "...";
        else
            return mb_substr($str, $start, $length, $charset);
    } elseif (function_exists('iconv_substr')) {
        if ($suffix)
            return iconv_substr($str, $start, $length, $charset) . "...";
        else
            return iconv_substr($str, $start, $length, $charset);
    }
    $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("", array_slice($match[0], $start, $length));
    if ($suffix)
        return $slice . "…";
    return $slice;
}

/**
 *
 * @param $arr_data 原数组            
 * @param $field 指定列            
 * @param $descending 是否降顺（默认升顺）            
 * @return 排列好的数组
 */
function array_sort_by_field($arr_data, $field, $descending = false)
{
    $arrSort = array();
    foreach ($arr_data as $key => $value) {
        $arrSort[$key] = $value[$field];
    }
    
    if ($descending) {
        arsort($arrSort);
    } else {
        asort($arrSort);
    }
    
    $resultArr = array();
    foreach ($arrSort as $key => $value) {
        $resultArr[$key] = $arr_data[$key];
    }
    
    return $resultArr;
}

/**
 * 获取输入参数 支持过滤和默认值 From ThinkPHP 系统函数库(I函数)
 * 使用方法:
 * <code>
 * input('id',0); 获取id参数 自动判断get或者post
 * input('post.name','','htmlspecialchars'); 获取$_POST['name']
 * input('get.'); 获取$_GET
 * </code>
 *
 * @param string $name
 *            变量的名称 支持指定类型
 * @param mixed $default
 *            不存在的时候默认值
 * @param mixed $filter
 *            参数过滤方法
 * @param mixed $datas
 *            要获取的额外数据源
 * @return mixed
 */
function input($name, $default = '', $filter = null, $datas = null)
{
    static $_PUT = null;
    if (strpos($name, '/')) { // 指定修饰符
        list ($name, $type) = explode('/', $name, 2);
    } else { // 默认强制转换为字符串
        $type = 's';
    }
    if (strpos($name, '.')) { // 指定参数来源
        list ($method, $name) = explode('.', $name, 2);
    } else { // 默认为自动判断
        $method = 'param';
    }
    switch (strtolower($method)) {
        case 'get':
            $input = & $_GET;
            break;
        case 'post':
            $input = & $_POST;
            break;
        case 'put':
            if (is_null($_PUT)) {
                parse_str(file_get_contents('php://input'), $_PUT);
            }
            $input = $_PUT;
            break;
        case 'param':
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $input = $_POST;
                    break;
                case 'PUT':
                    if (is_null($_PUT)) {
                        parse_str(file_get_contents('php://input'), $_PUT);
                    }
                    $input = $_PUT;
                    break;
                default:
                    $input = $_GET;
            }
            break;
        case 'path':
            $input = array();
            if (! empty($_SERVER['PATH_INFO'])) {
                $depr = '/';
                $input = explode($depr, trim($_SERVER['PATH_INFO'], $depr));
            }
            break;
        case 'request':
            $input = & $_REQUEST;
            break;
        case 'session':
            $input = & $_SESSION;
            break;
        case 'cookie':
            $input = & $_COOKIE;
            break;
        case 'server':
            $input = & $_SERVER;
            break;
        case 'globals':
            $input = & $GLOBALS;
            break;
        case 'data':
            $input = & $datas;
            break;
        default:
            return null;
    }
    if ('' == $name) { // 获取全部变量
        $data = $input;
        $filters = isset($filter) ? $filter : \Yaf\Registry::get('config')->user->default_filter;
        if ($filters) {
            if (is_string($filters)) {
                $filters = explode(',', $filters);
            }
            foreach ($filters as $filter) {
                $data = array_map_recursive($filter, $data); // 参数过滤
            }
        }
    } elseif (isset($input[$name])) { // 取值操作
        $data = $input[$name];
        $filters = isset($filter) ? $filter : \Yaf\Registry::get('config')->user->default_filter;
        if ($filters) {
            if (is_string($filters)) {
                if (0 === strpos($filters, '/')) {
                    if (1 !== preg_match($filters, (string) $data)) {
                        // 支持正则验证
                        return isset($default) ? $default : null;
                    }
                } else {
                    $filters = explode(',', $filters);
                }
            } elseif (is_int($filters)) {
                $filters = array(
                    $filters
                );
            }
            
            if (is_array($filters)) {
                foreach ($filters as $filter) {
                    if (function_exists($filter)) {
                        $data = is_array($data) ? array_map_recursive($filter, $data) : $filter($data); // 参数过滤
                    } else {
                        $data = filter_var($data, is_int($filter) ? $filter : filter_id($filter));
                        if (false === $data) {
                            return isset($default) ? $default : null;
                        }
                    }
                }
            }
        }
        if (! empty($type)) {
            switch (strtolower($type)) {
                case 'a': // 数组
                    $data = (array) $data;
                    break;
                case 'd': // 数字
                    $data = (int) $data;
                    break;
                case 'f': // 浮点
                    $data = (float) $data;
                    break;
                case 'b': // 布尔
                    $data = (boolean) $data;
                    break;
                case 's': // 字符串
                default:
                    $data = (string) $data;
            }
        }
    } else { // 变量默认值
        $data = isset($default) ? $default : null;
    }
    is_array($data) && array_walk_recursive($data, 'other_safe_filter');
    return $data;
}

/**
 * 其他安全过滤 From ThinkPHP 系统函数库 为input函数服务
 *
 * @param
 *            $value
 */
function other_safe_filter(&$value)
{
    // TODO 其他安全过滤
    // 过滤查询特殊字符
    if (preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i', $value)) {
        $value .= ' ';
    }
}

/**
 * 用于input函数的递归
 *
 * @param
 *            $filter
 * @param
 *            $data
 * @return array
 */
function array_map_recursive($filter, $data)
{
    $result = array();
    foreach ($data as $key => $val) {
        $result[$key] = is_array($val) ? array_map_recursive($filter, $val) : call_user_func($filter, $val);
    }
    return $result;
}

/**
 * 获取客户端IP地址 FROM ThinkPHP 系统函数库
 *
 * @param integer $type
 *            返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv
 *            是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false)
{
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL)
        return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos)
                unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array(
        $ip,
        $long
    ) : array(
        '0.0.0.0',
        0
    );
    return $ip[$type];
}

/**
 * GET 请求 FROM wechat-php-sdk
 *
 * @param string $url            
 */
function http_get($url)
{
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); // CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}

/**
 * POST 请求 FROM wechat-php-sdk
 *
 * @param string $url            
 * @param array $param            
 * @param boolean $post_file
 *            是否文件上传
 * @return string content
 */
function http_post($url, $param, $post_file = false)
{
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); // CURL_SSLVERSION_TLSv1
    }
    if (is_string($param) || $post_file) {
        $strPOST = $param;
    } else {
        $aPOST = array();
        foreach ($param as $key => $val) {
            $aPOST[] = $key . "=" . urlencode($val);
        }
        $strPOST = join("&", $aPOST);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POST, true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}

/**
 * curl 批处理
 *
 * @param
 *            $url_array
 * @return array
 * @author jsyzchenchen@gmail.com
 */
function curl_multi($data, $options = array())
{
    $handles = $contents = array();
    // 初始化curl multi对象
    $mh = curl_multi_init();
    // 添加curl 批处理会话
    foreach ($data as $key => $value) {
        $url = (is_array($value) && ! empty($value['url'])) ? $value['url'] : $value;
        $handles[$key] = curl_init($url);
        curl_setopt($handles[$key], CURLOPT_RETURNTRANSFER, 1);
        
        // 判断是否是post
        if (is_array($value)) {
            if (! empty($value['post'])) {
                curl_setopt($handles[$key], CURLOPT_POST, 1);
                curl_setopt($handles[$key], CURLOPT_POSTFIELDS, $value['post']);
            }
        }
        
        // extra options?
        if (! empty($options)) {
            curl_setopt_array($handles[$key], $options);
        }
        
        curl_multi_add_handle($mh, $handles[$key]);
    }
    // ======================执行批处理句柄=================================
    $active = null;
    do {
        $mrc = curl_multi_exec($mh, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    while ($active and $mrc == CURLM_OK) {
        if (curl_multi_select($mh) === - 1) {
            usleep(100);
        }
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    }
    // ====================================================================
    // 获取批处理内容
    foreach ($handles as $i => $ch) {
        $content = curl_multi_getcontent($ch);
        $contents[$i] = curl_errno($ch) == 0 ? $content : '';
    }
    // 移除批处理句柄
    foreach ($handles as $ch) {
        curl_multi_remove_handle($mh, $ch);
    }
    // 关闭批处理句柄
    curl_multi_close($mh);
    return $contents;
}

function isMobile($mobile)
{
    if (! is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}

function timediff($begin_time, $end_time)
{
    if ($begin_time < $end_time) {
        $starttime = $begin_time;
        $endtime = $end_time;
    } else {
        $starttime = $end_time;
        $endtime = $begin_time;
    }
    $timediff = $endtime - $starttime;
    $days = intval($timediff / 86400);
    $remain = $timediff % 86400;
    $hours = intval($remain / 3600);
    $remain = $remain % 3600;
    $mins = intval($remain / 60);
    $secs = $remain % 60;
    $res = array(
        "day" => $days,
        "hour" => $hours,
        "min" => $mins,
        "sec" => $secs
    );
    return $res;
}

/**
 * dateformat
 *
 *
 * @author kevin
 * @access public
 * @param mixed $code            
 * @return json
 *
 */
function dateformat($time)
{
    return date('Y-m-d', $time);
}

function getCityNameByIp($ip)
{
    if (empty($ip)) {
        return '';
    }
    $json_res = file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip={$ip}");
    $res = json_decode($json_res);
    
    return $res->city;
}

/**
 * 将时间戳转换成多长时间前，和日期
 *
 * @param int $time
 *            时间戳
 * @return string 日期，多少秒前，多少分钟前，多少小时前，多少天前
 */
function timeAgo($time, $format = "Y-m-d H:i")
{
    $diffTime = time() - $time;
    if ($diffTime < 60) {
        return $diffTime . '秒前';
    } elseif ($diffTime < 3600 && $diffTime >= 60) {
        return floor($diffTime / 60) . '分钟前';
    } elseif ($diffTime < (3600 * 24) && $diffTime >= 3600) {
        return floor($diffTime / 3600) . '小时前';
    } elseif ($diffTime > 3600 * 24 && $diffTime < 3600 * 24 * 30) {
        return floor($diffTime / (3600 * 24)) . '天前';
    } elseif ((date('Ym') == date('Ym', $time)) && $diffTime >= (3600 * 24)) {
        return floor($diffTime / (3600 * 24)) . '天前';
    } else {
        return date($format, $time);
    }
}

function timeDuration($time)
{
    if (intval($time) <= 0) {
        return '';
    }
    $diffTime = $time;
    if ($diffTime < 60) {
        return $diffTime . '秒';
    } elseif ($diffTime < 3600 && $diffTime >= 60) {
        return floor($diffTime / 60) . '分钟';
    } elseif ($diffTime < (3600 * 24) && $diffTime >= 3600) {
        return floor($diffTime / 3600) . '小时';
    }
}

function getDayTimeDiff($start_date, $end_date, $return_type)
{
    $timediff = strtotime($end_date) - strtotime($start_date);
    $days = intval($timediff / 86400);
    $remain = $timediff % 86400;
    $hours = intval($remain / 3600);
    $remain = $remain % 3600;
    $mins = intval($remain / 60);
    $secs = $remain % 60;
    switch ($return_type) {
        case "day":
            return $days;
            break;
        case "hour":
            return $hours;
            break;
        case "mins":
            return $mins;
            break;
        case "secs":
            return $secs;
            break;
        default:
            return 0;
    }
}

function filterParam($name, $params, $defaultValue = '')
{
    return array_key_exists($name, $params) ? urldecode($params[$name]) : $defaultValue;
}

function nndealParam($value)
{
    return urldecode($value);
}

/**
 * storeStatus
 * 店铺状态
 *
 * @access public
 * @param mixed $status
 *            状态
 * @return string
 */
function storeStatus($status)
{
    $statusStr = "";
    switch ($status) {
        case 0:
            $statusStr = '转租中';
            break;
        case 1:
            $statusStr = '营业中';
            break;
    }
    return $statusStr;
}

/**
 * getOperatorNameById
 * 获取业务员姓名
 *
 * @access public
 * @param mixed $id            
 * @return json
 */
function getOperatorNameById($id)
{
    $name = '';
    $row = DB::table('operator')->select('name')
        ->where('id', $id)
        ->first();
    if (! empty($row)) {
        $name = $row->name;
    }
    
    return $name;
}

function getAntOperatorNameById($id)
{
    $name = '';
    $row = DB::connection('ant')->table('operator')
        ->select('name')
        ->where('id', $id)
        ->first();
    if (! empty($row)) {
        $name = $row->name;
    }
    
    return $name;
}

function getOperatorThumbById($id)
{
    $thumb = '';
    $row = DB::table('operator')->select('thumb')
        ->where('id', $id)
        ->first();
    if (! empty($row)) {
        $thumb = $row->thumb;
    }
    return $thumb;
}

function getOperatorPhoneById($id)
{
    $phone = '';
    $row = DB::table('operator')->select('phone')
        ->where('id', $id)
        ->first();
    if (! empty($row)) {
        $phone = $row->phone;
    }
    return $phone;
}

/**
 * getStoreSnByStoreId
 * 获取店铺store_sn
 *
 * @author kevin
 * @access public
 * @param mixed $store_id            
 * @return json
 *
 */
function getStoreSnByStoreId($store_id)
{
    $storeSn = '';
    $rs = DB::table('store')->select('store_sn')
        ->where('id', $store_id)
        ->first();
    if (! empty($rs)) {
        $storeSn = $rs->store_sn;
    }
    return $storeSn;
}

/**
 * getInstallationNameById
 * 获取配套设施名
 *
 * @access public
 * @param mixed $id            
 * @return string
 */
function getInstallationNameById($id)
{
    $name = '';
    $rs = DB::connection('ant')->table('installation')
        ->select('name')
        ->where('id', $id)
        ->first();
    if (! empty($rs)) {
        $name = $rs->name;
    }
    return $name;
}

/**
 * getStoreStreetAndStreetSnById
 *
 *
 * @author kevin
 * @access public
 * @param mixed $code            
 * @return json
 *
 */
function getStoreStreetAndStreetSnById($storeId)
{
    $name = '';
    $row = DB::table('store')->select('street', 'street_sn')
        ->where('id', $storeId)
        ->first();
    if (! empty($row)) {
        $name = $row->street . $row->street_sn;
    }
    
    return $name;
}

/**
 * getAchievementMonthStoreNum
 * 本月店铺数
 *
 * @author kevin
 * @access public
 * @param mixed $operator_id
 *            业务员id
 * @param mixed $datetime
 *            2017-08
 * @return json
 *
 */
function getAchievementMonthStoreNum($operator_id, $datetime)
{
    $num = 0;
    $sql = "SELECT id FROM nn_store WHERE operator_id=? AND FROM_UNIXTIME(created_at,'%Y-%m')=?";
    $rows = DB::select($sql, [
        $operator_id,
        $datetime
    ]);
    $num = count($rows);
    return $num;
}

/**
 * getRentalMonthByDay
 * 根据天租金获取月租金
 *
 * @author kevin
 * @access public
 * @param mixed $rental_day
 *            天租金
 * @return json
 *
 */
function getRentalMonthByDay($rental_day, $build_area)
{
    $rental_month = 0;
    $rentalMonth = ($rental_day * 30 * $build_area) / 10000;
    
    // $rental_month = round($rentalMonth ,2);
    $rental_month = sprintf("%.2f", $rentalMonth);
    return $rental_month;
}

/**
 * getRentalYearByDay
 * 根据天租金获取年租金
 *
 * @author kevin
 * @access public
 * @param mixed $rental_day
 *            天租金
 * @return json
 *
 */
function getRentalYearByDay($rental_day, $build_area)
{
    $rental_year = 0;
    
    $rentalYear = ($rental_day * 30 * 12 * $build_area) / 10000;
    // $rental_year = round($rentalYear,2);
    $rental_year = sprintf("%.2f", $rentalYear);
    return $rental_year;
}

/**
 * getRentalDayByMonth
 * 根据月租、金获取天租金
 *
 * @author kevin
 * @access public
 * @param mixed $rental_month            
 * @return json
 *
 */
function getRentalDayByMonth($rental_month, $build_area)
{
    $rental_day = 0;
    
    $rentalDay = ($rental_month / 12 / $build_area) * 10000;
    // $rental_day = round($rentalDay,2);
    $rental_day = sprintf("%.2f", $rentalDay);
    return $rental_day;
}

/**
 * getRentalYearByMonth
 * 根据月租获取年租金
 *
 * @author kevin
 * @access public
 * @param mixed $rental_month            
 * @return json
 *
 */
function getRentalYearByMonth($rental_month)
{
    $rental_year = 0;
    $rentalYear = $rental_month * 12;
    // $rental_year = round($rentalYear,2);
    $rental_year = sprintf("%.2f", $rentalYear);
    return $rental_year;
}

/**
 * getRentalDayByYear
 * 根据年租金获取天租金
 *
 * @author kevin
 * @access public
 * @param mixed $rental_year            
 * @return json
 *
 */
function getRentalDayByYear($rental_year, $build_area)
{
    $rental_day = 0;
    $rentalDay = ($rental_year / 12 / 30 / $build_area) * 10000;
    // $rental_day = round($rentalDay,2);
    $rental_day = sprintf("%.2f", $rentalDay);
    return $rental_day;
}

/**
 * getRentalMonthByYear
 * 根据年租金获取月租金
 *
 * @author kevin
 * @access public
 * @param mixed $rental_year            
 * @return json
 *
 */
function getRentalMonthByYear($rental_year)
{
    $rental_month = 0;
    
    $rentalMonth = $rental_year / 12;
    // $rental_month = round($rentalMonth,2);
    $rental_month = sprintf("%.2f", $rentalMonth);
    return $rental_month;
}

/**
 * hidePhone
 * 隐藏手机号码
 *
 * @author kevin
 * @access public
 * @param mixed $phone            
 * @return json
 *
 */
function hidePhone($phone)
{
    $phone = substr_replace($phone, '****', 3, 4);
    return $phone;
}

/**
 * makeStoreSn
 *
 *
 * @author kevin
 * @access public
 * @param mixed $code            
 * @return json
 *
 */
function makeStoreSn($district)
{
    $storeSn = '';
    $code = '0000';
    $row = DB::table('area')->select('code')
        ->where('name', $district)
        ->first();
    if (! empty($row)) {
        $code = $row->code;
    }
    $storeSn = 'F' . $code . substr(time() . mt_rand(1, 1000000), 8, 3);
    
    /**
     * $redis = NndealRedis::getInstance();
     * if( !$redis->exists('nndeal_sn_'.$storeSn) ){
     * $redis->set('nndeal_sn_'.$storeSn,$storeSn);
     * return $storeSn;
     * }else{
     * makeStoreSn($district);
     * }
     */
    $cnt = DB::table('store')->where('store_sn', $storeSn)->count();
    if ($cnt > 0) {
        makeStoreSn($district);
    }
    return $storeSn;
}

/**
 * getWorkDay
 * 获取指定日期内工作日
 *
 * @author kevin
 * @access public
 * @param mixed $start
 *            2017-08-01
 * @param mixed $end
 *            2017-08-11
 * @return array
 *
 */
function getWorkDay($start, $end)
{
    $work_arr = [];
    
    $begin_at = strtotime($start);
    $end_at = strtotime($end);
    
    $holidays = array(); // 节假日
    $job_days = array(); // 特殊工作日 如周六
    
    $redis = NndealRedis::getInstance();
    
    $nn_holiday_days = unserialize($redis->get('holiday_days'));
    if (empty($nn_holiday_days)) {
        $holidaysArr = DB::table('holiday')->select('hday')->get();
        foreach ($holidaysArr as $rs) :
            
            array_push($holidays, date('Y-m-d', $rs->hday));
        endforeach
        ;
        $redis->set('holiday_days', serialize($holidays));
    } else {
        $holidays = unserialize($redis->get('holiday_days'));
    }
    
    if (empty($redis->get('job_days'))) {
        $jobdaysArr = DB::table('job_day')->select('hday')->get();
        foreach ($jobdaysArr as $rs) :
            array_push($job_days, date('Y-m-d', $rs->hday));
        endforeach
        ;
        $redis->set('job_days', serialize($job_days));
    } else {
        $job_days = unserialize($redis->get('job_days'));
    }
    
    while ($begin_at <= $end_at) {
        if (in_array(date('Y-m-d', $begin_at), $job_days)) {
            array_push($work_arr, date('Y-m-d', $begin_at));
        } else {
            if (! in_array(date('Y-m-d', $begin_at), $holidays)) {
                if (date('w', $begin_at) != 0 && date('w', $begin_at) != 6) {
                    array_push($work_arr, date('Y-m-d', $begin_at));
                }
            }
        }
        
        $begin_at = strtotime('+1 day', $begin_at);
    }
    
    return $work_arr;
}

/**
 * getAchievementMonthNoStoreDays
 * 本月无店铺工作日
 *
 * @author kevin
 * @access public
 * @param mixed $operator_id
 *            业务员id
 * @param mixed $datetime
 *            月份 2017-08
 * @return json
 *
 */
function getAchievementMonthNoStoreDays($operator_id, $datetime)
{
    $str = '';
    $start_day = $datetime . "-" . '01';
    $end_day = date('Y-m-d', strtotime("$datetime +1 month -1 day"));
    $today = date('Y-m-d');
    if (strtotime($today) <= strtotime($end_day)) {
        $end_day = $today;
    }
    $workDays = getWorkDay($start_day, $end_day); // 获取工作日array
    
    $storeMonths = array(); // 本月录入店铺日期array
    $sql = "select created_at from nn_store where operator_id=? and FROM_UNIXTIME(created_at,'%Y-%m')=?";
    $myStores = DB::select($sql, [
        $operator_id,
        $datetime
    ]);
    $job_stored = array();
    if (! empty($myStores)) {
        foreach ($myStores as $s) :
            array_push($job_stored, date('Y-m-d', $s->created_at));
        endforeach
        ;
    }
    $no_store_job_days = array_diff($workDays, $job_stored);
    if (! empty($no_store_job_days)) {
        foreach ($no_store_job_days as $noStoreDay) :
            $str .= "、" . floatval(date('d', strtotime($noStoreDay)));
        endforeach
        ;
    }
    
    return trim($str, '、');
}

/**
 * 分页函数 核心函数 array_slice
 * 用此函数之前要先将数据库里面的所有数据按一定的顺序查询出来存入数组中
 * $count 每页展示多少数据
 * $page 当前第几页
 * $array 分页数组
 * $order 0 - 不变 1 - 反序
 */
function page_array($page, $array, $count = 10, $order = 0)
{
    $page = (empty($page)) ? 1 : $page; // 判断是否为空如果为空展示第一页
    $start = ($page - 1) * $count; // 计算每次分页的开始位置
    if ($order == 1) {
        $array = array_reverse($array);
    }
    $totals = count($array);
    $countpage = ceil($totals / $count); // 计算总页面数
    $pagedata = array();
    $pagedata = array_slice($array, $start, $count);
    return $pagedata; // 返回查询数据
}

function nndeal_mkdir($dirName, $rights = 0777)
{
    $dirs = explode('/', $dirName);
    $dir = '';
    foreach ($dirs as $part) {
        $dir .= $part . '/';
        if (! is_dir($dir) && strlen($dir) > 0)
            mkdir($dir, $rights);
    }
}

/**
 * getOperatorChild
 * 获取所有操作员
 *
 * @author kevin
 * @access public
 * @param mixed $code            
 * @return array
 *
 */
function getOperatorChild($leaderId, $idstr = '')
{
    $sql = "select * from nn_operator_relation where leader_id=?";
    $results = DB::select($sql, [
        $leaderId
    ]);
    foreach ($results as $row) :
        
        $operatorId = $row->operator_id;
        $idstr .= $operatorId . ",";
        $res = DB::select("select * from nn_operator_relation where leader_id=?", [
            $operatorId
        ]);
        if (count($res) > 0) {
            
            foreach ($res as $r) :
                $idstr .= $r->operator_id . ",";
            endforeach
            ;
        }
    endforeach
    ;
    
    $idstr = trim($idstr, ',');
    $arr = explode(',', $idstr);
    $arr = array_unique($arr);
    return $arr;
}

/*
 * Functionality: Generate Single-language[Chinese-simplified] pagenation navigator
 * @Params:
 * Int $page: current page
 * Int $totalPages: total pages
 * String $URL: target URL for pagenation
 * Int $count: total records
 * String $query: query string for SEARCH
 * @Return: String pagenation navigator link
 */
function Page($page, $totalPages, $URL, $counts, $query = '')
{
    $page = $page ? $page : 1;
    $URL .= (strpos($URL, '?') === FALSE ? '?' : '&');
    $link = '<ul class="pagination pull-right no-margin">';
    if ($page == 1) {
        $link .= '<li class="prev disabled">
                    <a href="javascript:void(0);">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </li>';
    } else {
        $prev = $URL . 'page=' . ($page - 1) . $query;
        $link .= '<li class="prev">
                    <a href="' . $prev . '">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </li>';
    }
    $sep = FALSE;
    // 超过 10 页则要考虑前二后二中四
    if ($totalPages > 10) {
        $sep = TRUE;
    }
    $first = $URL . 'page=1' . $query;
    if ($page == 1) {
        $active = 'active';
    } else {
        $active = '';
    }
    $link .= '<li class="' . $active . '">
                <a href="' . $first . '">1</a>
            </li>';
    if ($totalPages >= 2) {
        $second = $URL . 'page=2' . $query;
        if ($page == 2) {
            $active = 'active';
        } else {
            $active = '';
        }
        $link .= '<li class="' . $active . '">
                    <a href="' . $second . '">2</a>
                </li>';
    }
    if ($sep) {
        if (($page - 2) > 2) {
            $link .= '<li>
                   <a href="' . $URL . 'page=' . ($page - 3) . $query . '">...</a>
                </li>';
        }
        // 取中间四个
        for ($i = ($page - 2); $i <= ($page + 2); $i ++) {
            if ($i <= 2 || $i > ($totalPages - 2)) {
                continue;
            }
            if ($i > $totalPages) {
                break;
            }
            if ($page == ($totalPages - 1)) {
                break;
            }
            $p = $URL . 'page=' . $i . $query;
            if ($page == $i) {
                $active = 'active';
            } else {
                $active = '';
            }
            $link .= '<li class="' . $active . '">
                        <a href="' . $p . '">' . $i . '</a>
                    </li>';
        }
        if (($page + 2) < ($totalPages - 2)) {
            $link .= '<li>
                    <a href="' . $URL . 'page=' . ($page + 3) . $query . '">...</a>
                </li>';
        }
    } else {
        for ($i = 3; $i <= ($totalPages - 2); $i ++) {
            $p = $URL . 'page=' . $i . $query;
            if ($page == $i) {
                $active = 'active';
            } else {
                $active = '';
            }
            $link .= '<li class="' . $active . '">
                        <a href="' . $p . '">' . $i . '</a>
                    </li>';
        }
    }
    if ($totalPages > 2) {
        if (($totalPages - 1) != 2) {
            $p = $URL . 'page=' . ($totalPages - 1) . $query;
            if ($page == ($totalPages - 1)) {
                $active = 'active';
            } else {
                $active = '';
            }
            $link .= '<li class="' . $active . '">
                    <a href="' . $p . '">' . ($totalPages - 1) . '</a>
                </li>';
        }
        $p = $URL . 'page=' . $totalPages . $query;
        if ($page == $totalPages) {
            $active = 'active';
        } else {
            $active = '';
        }
        $link .= '<li class="' . $active . '">
                    <a href="' . $p . '">' . $totalPages . '</a>
                </li>';
    }
    if ($page == $totalPages) {
        $link .= '<li class="next disabled">
            <a href="javascript:void(0);">
                <i class="fa fa-angle-right"></i>
            </a>
        </li>';
    } else {
        $next = $URL . 'page=' . ($page + 1) . $query;
        $link .= '<li class="next">
                    <a href="' . $next . '">
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>';
    }
    $link .= '</ul>';
    return $link;
}

/**
 * getNetStoreSource
 *
 *
 * @author kevin
 * @access public
 * @param mixed $code            
 * @return json
 *
 */
function getNetStoreSource($source)
{
    $sourceStr = '';
    switch ($source) {
        case 1:
            $sourceStr = '58同城';
            break;
        case 2:
            $sourceStr = '赶集网';
            break;
        case 3:
            $sourceStr = '安居客';
            break;
        case 4:
            $sourceStr = '房天下';
            break;
        case 5:
            $sourceStr = '上上铺';
            break;
        case 6:
            $sourceStr = '铺铺旺';
            break;
        case 7:
            $sourceStr = '优+商铺';
            break;
        case 8:
            $sourceStr = '链家网 ';
            break;
        default:
            $sourceStr = '';
    }
    
    return $sourceStr;
}

/**
 * 计算某个经纬度的周围某段距离的正方形的四个点
 *
 * @param
 *            lng float 经度
 * @param
 *            lat float 纬度
 * @param
 *            distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
 * @return array 正方形的四个点的经纬度坐标
 */
function returnSquarePoint($lng, $lat, $distance = 0.5)
{
    // defined(EARTH_RADIUS) || define(EARTH_RADIUS, 6371);//地球半径，平均半径为6371km
    $dlng = 2 * asin(sin($distance / (2 * 6371)) / cos(deg2rad($lat)));
    $dlng = rad2deg($dlng);
    
    $dlat = $distance / 6371;
    $dlat = rad2deg($dlat);
    
    return array(
        'left-top' => array(
            'lat' => $lat + $dlat,
            'lng' => $lng - $dlng
        ),
        'right-top' => array(
            'lat' => $lat + $dlat,
            'lng' => $lng + $dlng
        ),
        'left-bottom' => array(
            'lat' => $lat - $dlat,
            'lng' => $lng - $dlng
        ),
        'right-bottom' => array(
            'lat' => $lat - $dlat,
            'lng' => $lng + $dlng
        )
    );
}

/**
 * getUserSubscribe
 *
 *
 * @author kevin
 * @access public
 * @param mixed $code            
 * @return json
 *
 */
function getUserSubscribe($userId)
{
    $subscribe = '';
    $row = DB::table('subscribe')->where('user_id', $userId)->first();
    if (! empty($row)) {
        if (! empty($row->district)) {
            $subscribe .=  $row->district;
        }
        if (! empty($row->area)) {
            $area = '';
            switch ($row->area) {
                case 1: // 1-50平米
                    $area = "  1-50平米 ";
                    break;
                case 2: // 50-100平米
                    $area = " 50-100平米 ";
                    break;
                case 3: // 100-200平米
                    $area = " 100-200平米 ";
                    break;
                case 4: // 200-300平米
                    $area = " 200-300平米 ";
                    break;
                case 5: // 300-500平米
                    $area = " 300-500平米 ";
                    break;
                case 6: // >500平米
                   $area = " >500平米 ";
                    break;
            }
            
            $subscribe .= " + " . $area;
        }
        if (! empty($row->trade)) {
            $subscribe .= " + " . $row->trade;
        }
        if (! empty($row->rental)) {
            //$subscribe .= "租金：" . $row->rental;
            
            switch ($row->rental) {
                case 1: // 1-10万
                    $subscribe.=" + 1-10万 ";
                    break;
                case 2: // 10-20万
                    $subscribe.=" + 10-20万 ";
                    break;
                case 3: // 20-40万
                    $subscribe.=" + 20-40万 ";
                    break;
                case 4: // 40-60万
                    $subscribe.=" + 40-60万 ";
                    break;
                case 5: // 60-100万
                    $subscribe.=" + 60-100万 ";
                    break;
                case 6: // >100万
                    $subscribe.=" + >100万 ";
                    break;
            }
            
        }
        if (! empty($row->transfer_fee)) {
            $subscribe .= " + 无转让费" ;
        }
        if (! empty($row->updated_at)) {
            //$subscribe .= "更新时间：" . $row->updated_at;
            switch ($row->updated_at) {
                case 1: // 3天内 DATE_FORMAT(NOW(),'%Y-%m-%d')
                    $subscribe .= " + 3天内" ;
                    break;
                case 2: // 7天内
                    $subscribe .= " + 7天内" ;
                    break;
                case 3: // 30天内
                    $subscribe .= " + 30天内" ;
                    break;
            }
            
        }
        if (! empty($row->property)) {
            $subscribe .= " + " . $row->property;
        }
        if (! empty($row->tags)) {
            $subscribe .= " + " . $row->tags;
        }
    }
    return empty($subscribe) ? '暂无订阅' : $subscribe;
}

/**
 * getUserTodayBrower
 *
 *
 * @author kevin
 * @access public
 * @param mixed $code            
 * @return json
 *
 */
function getUserTodayBrower($userId)
{
    $num = 0;
    
    $sql = "select count(id) as vNum from network_visits where user_id=? and FROM_UNIXTIME(created_at,'%Y-%m-%d')=?";
    $res = DB::select($sql, [
        $userId,
        date('Y-m-d')
    ]);
    if (! empty($res)) {
        $num = $res[0]->vNum;
    }
    return $num;
}

/**
 * getUserSevenBrower
 *
 *
 * @author kevin
 * @access public
 * @param mixed $code            
 * @return json
 *
 */
function getUserSevenBrower($userId)
{
    $num = 0;
    $sql = "select count(id) as vNum from network_visits where user_id=? and DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'),FROM_UNIXTIME(created_at,'%Y-%m-%d'))<=7";
    $res = DB::select($sql, [
        $userId
    ]);
    if (! empty($res)) {
        $num = $res[0]->vNum;
    }
    return $num;
}

/**
 * updateUserSubscribe
 *
 *
 * @author kevin
 * @access public
 * @param mixed $code            
 * @return json
 *
 */
function updateUserSubscribe($userId, $district, $area, $rental, $transfer, $updated_at)
{
    $flag = false;
    
    $cnt = DB::table('subscribe')->where('user_id', $userId)->count();
    $data = [];
    if ($cnt > 0) {
        if (! empty($district) || $area >= 1 || $rental >= 1 || $transfer >= 1 || $updated_at >= 1) {
            if (! empty($district)) {
                $data['district'] = $district;
            }
            if ($area >= 1) {
                $data['area'] = $area;
            }
            if ($rental >= 1) {
                $data['rental'] = $rental;
            }
            if ($transfer >= 1) {
                $data['transfer_fee'] = $transfer;
            }
            if ($updated_at >= 1) {
                $data['updated_at'] = $updated_at;
            }
            $flag = DB::table('subscribe')->where('user_id', $userId)->update($data);
        }
    } else {
        if (! empty($district) || $area >= 1 || $rental >= 1 || $transfer >= 1 || $updated_at >= 1) {
            if (! empty($district)) {
                $data['district'] = $district;
            }
            if ($area >= 1) {
                $data['area'] = $area;
            }
            if ($rental >= 1) {
                $data['rental'] = $rental;
            }
            if ($transfer >= 1) {
                $data['transfer_fee'] = $transfer;
            }
            if ($updated_at >= 1) {
                $data['updated_at'] = $updated_at;
            }
            $data['user_id'] = $userId;
            $flag = DB::table('subscribe')->insert($data);
        }
    }
    
    return $flag;
}

function jpushFavoriateStoreUpdated($users,$updateStoreNum)
{
    $isSend = false;
    $logFileDir = dirname(getcwd()) . "/jpushlog/" . date("Y-m-d");
    $logFileName = 'mofangpu_jpush.log';
    nndeal_mkdir($logFileDir);
    $jpush = new NndealJpush();
    $time = time();
    
    foreach ($users as $rs) :
        
        $store = DB::connection('net')->table('store')
            ->select('title')
            ->where('id', $rs->store_id)
            ->first();
        
        $message = "您收藏的“" . $store->title . "”,信息已更新。";
        try {
            
            $res = $jpush->push(array(
                'ios',
                'android'
            ), 'mofangpu_' . $rs->user_id, $message, array(
                'type' => 1
            ));
            
            $file = fopen($logFileDir . "/" . $logFileName, "a+");
            fwrite($file, date('Y-m-d H:i:s') . "favoriateStoreUpdate--ok--:" . print_r(array(
                'type' => 1,
                'message' => $message,
                'user_id' => $rs->user_id,
                'store_id' => $rs->store_id,
                'created_at' => $time
            ), true) . "\r");
            fwrite($file, date('Y-m-d H:i:s') . ":" . print_r($res, true) . "\n\r\n\r");
            fclose($file);
            
            DB::table('message')->insert(array(
                'type' => 1,
                'title' => $message,
                'message' => "今日有{$updateStoreNum}个新的相关商铺",
                'user_id' => $rs->user_id,
                'store_id' => $rs->store_id,
                'created_at' => $time,
                'stype'=>2
            ));
        } catch (\Exception $ex) {
            
            $file = fopen($logFileDir . "/" . $logFileName, "a+");
            fwrite($file, date('Y-m-d H:i:s') . "favoriateStoreUpdate--error--:" . print_r(array(
                'type' => 1,
                'message' => $message,
                'user_id' => $rs->user_id,
                'store_id' => $rs->store_id,
                'created_at' => $time
            ), true) . "\r");
            fwrite($file, date('Y-m-d H:i:s') . ":" . $ex->getMessage() . "\n\r\n\r");
            fclose($file);
        }
        
        if ($res['http_code'] == 200) {
            $isSend = true;
        }
    endforeach
    ;
    return $isSend;
}


function jpushSingleFavoriateStoreUpdated($users)
{
    $isSend = false;
    $logFileDir = dirname(getcwd()) . "/jpushlog/" . date("Y-m-d");
    $logFileName = 'mofangpu_jpush.log';
    nndeal_mkdir($logFileDir);
    $jpush = new NndealJpush();
    $time = time();
    
    foreach ($users as $rs) :
    
    $store = DB::connection('ant')->table('store')
    ->where('id', $rs->store_id)
    ->first();
    
    $statusStr = '';
    switch ($store->status) {
        case 0:
            $statusStr = '转租中';
            break;
        case 1:
            $statusStr = '营业中';
            break;
    }
    $title = $store->district . $store->street . "·" . $store->build_area . "㎡" . $store ->present_operation . $statusStr;
    
    $message = "您收藏的“" . $title . "”,信息已更新。";
    
    try {
        
        $res = $jpush->push(array(
            'ios',
            'android'
        ), 'mofangpu_' . $rs->user_id, $message, array(
            'type' => 2,
            'store_id' => $rs->store_id,
            'stype' =>1
        ));
        
        $file = fopen($logFileDir . "/" . $logFileName, "a+");
        fwrite($file, date('Y-m-d H:i:s') . "favoriateStoreUpdate--ok--:" . print_r(array(
            'type' => 2,
            'message' => $message,
            'user_id' => $rs->user_id,
            'store_id' => $rs->store_id,
            'created_at' => $time,
            'stype' => 2
        ), true) . "\r");
        fwrite($file, date('Y-m-d H:i:s') . ":" . print_r($res, true) . "\n\r\n\r");
        fclose($file);
        
        DB::table('message')->insert(array(
            'type' => 2,
            'title' => $message,
            'message' => "店铺信息更新",
            'user_id' => $rs->user_id,
            'store_id' => $rs->store_id,
            'created_at' => $time,
            'stype'=>2
        ));
    } catch (\Exception $ex) {
        
        $file = fopen($logFileDir . "/" . $logFileName, "a+");
        fwrite($file, date('Y-m-d H:i:s') . "favoriateStoreUpdate--error--:" . print_r(array(
            'type' => 2,
            'message' => $title,
            'user_id' => $rs->user_id,
            'store_id' => $rs->store_id,
            'created_at' => $time,
            'stype' => 2
        ), true) . "\r");
        fwrite($file, date('Y-m-d H:i:s') . ":" . $ex->getMessage() . "\n\r\n\r");
        fclose($file);
    }
    
    if ($res['http_code'] == 200) {
        $isSend = true;
    }
    endforeach
    ;
    return $isSend;
}


/**
 * jpushUserSubcribeUpdated
 *
 *
 * @author kevin
 * @access public
 * @param mixed $code            
 * @return json
 *
 */
function jpushUserSubcribeUpdated($users,$updateStoreNum)
{
    $isSend = false;
    $logFileDir = dirname(getcwd()) . "/jpushlog/" . date("Y-m-d");
    $logFileName = 'mofangpu_jpush.log';
    nndeal_mkdir($logFileDir);
    $jpush = new NndealJpush();
    $time = time();
    
    foreach ($users as $user) :
        
        $store = DB::connection('net')->table('subscribe')
            ->where('user_id', $user->user_id)
            ->first();
        
        $subrole = '';
        if (!empty($store->district)){
            $subrole.=$store->district." + ";
        }
        
        if (intval($store->area)>0){
            switch (intval($store->area)) {
                case 1: // 1-50平米
                    $subrole.="1-50㎡ + ";
                    break;
                case 2: // 50-100平米
                    $subrole.="50-100㎡ + ";
                    break;
                case 3: // 100-200平米
                    $subrole.="100-200㎡ + ";
                    break;
                case 4: // 200-300平米
                    $subrole.="200-300㎡ + ";
                    break;
                case 5: // 300-500平米
                    $subrole.="300-500㎡ + ";
                    break;
                case 6: // >500平米
                    $subrole.=">500㎡ + ";
                    break;
            }
        }
        
        if (intval($store->rental)>0){
            switch (intval($store->rental)) {
                case 1: // 1-10万
                    $subrole.="1-10万 + ";
                    break;
                case 2: // 10-20万
                    $subrole.="10-20万 + ";
                    break;
                case 3: // 20-40万
                    $subrole.="20-40万 + ";
                    break;
                case 4: // 40-60万
                    $subrole.="40-60万 + ";
                    break;
                case 5: // 60-100万
                    $subrole.="60-100万 + ";
                    break;
                case 6: // >100万
                    $subrole.=">100万 + ";
                    break;
            }
        }
        
        if (intval($store->transfer_fee)==1){
            $subrole.="无转让费 + ";
        }
        
        if (intval($store->updated_at)>0){
            switch (intval($store->updated_at)) {
                case 1: // 3天内 DATE_FORMAT(NOW(),'%Y-%m-%d')
                    $subrole.="3天内 + ";
                    break;
                case 2: // 7天内
                    $subrole.="7天内 + ";
                    break;
                case 3: // 30天内
                    $subrole.="30天内 + ";
                    break;
            }
        }
        
        $message = "您订阅的“" . $subrole . "”有更新啦";
        try {
            
            $res = $jpush->push(array(
                'ios',
                'android'
            ), 'mofangpu_' . $user->user_id, $message, array(
                'type' => 2
            ));
            
            $file = fopen($logFileDir . "/" . $logFileName, "a+");
            fwrite($file, date('Y-m-d H:i:s') . "subscribeStoreUpdate--ok--:" . print_r(array(
                'type' => 2,
                'message' => $message,
                'user_id' => $user->user_id,
                'subrole' => $subrole,
                'created_at' => $time
            ), true) . "\r");
            fwrite($file, date('Y-m-d H:i:s') . ":" . print_r($res, true) . "\n\r\n\r");
            fclose($file);
            
            DB::table('message')->insert(array(
                'type' => 1,
                'message' => "今日有{$updateStoreNum}个新的相关商铺",
                'user_id' => $user->user_id,
                'store_id' => 0,
                'created_at' => $time,
                'title'=>$subrole.$message,
                'stype'=>1
            ));
        } catch (\Exception $ex) {
            
            $file = fopen($logFileDir . "/" . $logFileName, "a+");
            fwrite($file, date('Y-m-d H:i:s') . "subscribeStoreUpdate--error--:" . print_r(array(
                'type' => 2,
                'message' => $message,
                'user_id' => $user->user_id,
                'subrole' => $subrole,
                'created_at' => $time
            ), true) . "\r");
            fwrite($file, date('Y-m-d H:i:s') . ":" . $ex->getMessage() . "\n\r\n\r");
            fclose($file);
        }
        
        if ($res['http_code'] == 200) {
            $isSend = true;
        }
    endforeach
    ;
    
    return $isSend;
}

/**
* getNearlySchool
* 
*
* @author kevin
* @access public
* @param mixed $code    
* @return json
*/
function getNearlySchool($lng,$lat) {
    $schools = [];
    
    $url = "http://restapi.amap.com/v3/place/around?key=050e4206e58b0c01617716357b3a05f6&location=$lng,$lat&keywords=学校&types=141200&radius=1000&offset=300&page=1&extensions=all";
    
    $contents = http_get($url);
    
    $res = json_decode($contents);
    
    if($res->status==1 && is_object($res)){
        
        //echo $res->count;
        foreach ($res->pois as $row):
        
        //echo $row->name."<Br>";
        //echo $row->location."<br>";
        $location = $row->location;
        $loc = explode(',', $location);
        
        $schools[] = array(
            'name'=>$row->name,
            'location_lng'=>$loc[0],
            'location_lat'=>$loc[1]
        );
        
        endforeach;
    }
    return $schools;
}

/**
* getNearlyBus
* 
*
* @author kevin
* @access public
* @param mixed $code    
* @return json
*/
function getNearlyBus($lng,$lat) {
    $bus = [];
    
    $url = "http://restapi.amap.com/v3/place/around?key=050e4206e58b0c01617716357b3a05f6&location=$lng,$lat&keywords=公交站&types=150700&radius=1000&offset=300&page=1&extensions=all";
    
    $contents = http_get($url);
    
    $res = json_decode($contents);
    
    if($res->status==1 && is_object($res)){
        
        //echo $res->count;
        foreach ($res->pois as $row):
        
        //echo $row->name."<Br>";
        //echo $row->location."<br>";
        //echo $row->address."<br>";
        
        $location = $row->location;
        $loc = explode(',', $location);
        $bus[] = array(
            'name'=>$row->name,
            'address'=>$row->address,
            'location_lng'=>$loc[0],
            'location_lat'=>$loc[1]
        );
        
        endforeach;
    }
    
    return $bus;
}

/**
* getNearlyOffices
* 
*
* @author kevin
* @access public
* @param mixed $code    
* @return json
*/
function getNearlyOffices($lng,$lat) {
    $offices = [];
    $url = "http://restapi.amap.com/v3/place/around?key=050e4206e58b0c01617716357b3a05f6&location=$lng,$lat&keywords=写字楼&types=120200&radius=1000&offset=300&page=1&extensions=all";
    
    $contents = http_get($url);
    
    $res = json_decode($contents);
    
    if($res->status==1 && is_object($res)){
        
        //echo $res->count;
        foreach ($res->pois as $row):
        
        //echo $row->name."<Br>";
        //echo $row->location."<br>";
        $location = $row->location;
        $loc = explode(',', $location);
        
        $offices[] = array(
            'name'=>$row->name,
            'location_lng'=>$loc[0],
            'location_lat'=>$loc[1]
        );
        
        endforeach;
    }
    return $offices;
}
