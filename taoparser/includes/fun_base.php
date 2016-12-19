<?php

function Char_cv($msg){
	$msg = str_replace('%20','',$msg);
	$msg = str_replace('%27','',$msg);
	$msg = str_replace('*','',$msg);
	$msg = str_replace("\"",'',$msg);
//	$msg = str_replace('//','',$msg);
	$msg = str_replace('&amp;','&',$msg);
	$msg = str_replace('&nbsp;',' ',$msg);
	$msg = str_replace(';','',$msg);
	$msg = str_replace('"','&quot;',$msg);
	$msg = str_replace("'",'&#039;',$msg);
	$msg = str_replace("<","&lt;",$msg);
	$msg = str_replace(">","&gt;",$msg);
	$msg = str_replace('(','',$msg);
	$msg = str_replace(')','',$msg);
	$msg = str_replace("{",'',$msg);
	$msg = str_replace('}','',$msg);
	$msg = str_replace("\t","   &nbsp;  &nbsp;",$msg);
	$msg = str_replace("\r","",$msg);
	$msg = str_replace("   "," &nbsp; ",$msg);
	return $msg;
}


function HtmlReplace($str,$rptype=0)
{
	$str = stripslashes($str);
	if($rptype==0)
	{
		$str = htmlspecialchars($str);
	}
	else if($rptype==1)
	{
		$str = htmlspecialchars($str);
		$str = str_replace("　",' ',$str);
		$str = ereg_replace("[\r\n\t ]{1,}",' ',$str);
	}
	else if($rptype==2)
	{
		$str = htmlspecialchars($str);
		$str = str_replace("　",'',$str);
		$str = ereg_replace("[\r\n\t ]",'',$str);
	}
	else
	{
		$str = ereg_replace("[\r\n\t ]{1,}",' ',$str);
		$str = eregi_replace('script','ｓｃｒｉｐｔ',$str);
		$str = eregi_replace("<[/]{0,1}(link|meta|ifr|fra)[^>]*>",'',$str);
	}
	return addslashes($str);
}
/**
 * 批量初始化POST or GET变量,并数组返回
 *
 * @param Array $keys
 * @param string $method
 * @param var $htmcv
 * @return Array
 */
function Init_GP($keys,$method='GP',$htmcv=0){
	!is_array($keys) && $keys = array($keys);
	$array = array();
	foreach($keys as $val){
		$array[$val] = NULL;
		if($method!='P' && isset($_GET[$val])){
			$array[$val] = $_GET[$val];
		} elseif($method!='G' && isset($_POST[$val])){
			$array[$val] = $_POST[$val];
		}
		$htmcv && $array[$val] = Char_cv($array[$val]);
	}
	return $array;
}

/**
 * 批量初始化POST or GET变量,并将变量全局化
 *
 * @param Array $keys
 * @param string $method
 * @param var $htmcv
 */
function InitGP($keys,$method='GP',$htmcv=0){
	!is_array($keys) && $keys = array($keys);
	foreach($keys as $val){
		$GLOBALS[$val] = NULL;
		if($method!='P' && isset($_GET[$val])){
			$GLOBALS[$val] = $_GET[$val];
		} elseif($method!='G' && isset($_POST[$val])){
			$GLOBALS[$val] = $_POST[$val];
		}
		$htmcv && $GLOBALS[$val] = Char_cv($GLOBALS[$val]);
	}
}

/**
 * 初始化单一POST or GET 变量
 *
 * @param string $key
 * @param string $method
 * @return unknown
 */
function GetGP($key,$method='GP'){
	if($method=='G' || $method!='P' && isset($_GET[$key])){
		return $_GET[$key];
	}
	return $_POST[$key];
}
//处理数字类型字符
function GetNum($fnum)
{
	$nums = array("０","１","２","３","４","５","６","７","８","９");
	//$fnums = "0123456789";
	$fnums = array("0","1","2","3","4","5","6","7","8","9");
	$fnum = str_replace($nums,$fnums,$fnum);
	$fnum = ereg_replace("[^0-9\.-]",'',$fnum);
	if($fnum=='')
	{
		$fnum=0;
	}
	return $fnum;
}


//获得当前的脚本网址
function geturl()
{
	if(!empty($_SERVER["REQUEST_URI"]))
	{
		$scriptName = $_SERVER["REQUEST_URI"];
		$nowurl = $scriptName;
	}
	else
	{
		$scriptName = $_SERVER["PHP_SELF"];
		if(empty($_SERVER["QUERY_STRING"]))
		{
			$nowurl = $scriptName;
		}
		else
		{
			$nowurl = $scriptName."?".$_SERVER["QUERY_STRING"];
		}
	}
	return $nowurl;
}

//如果$string不是变量，则返回加上‘’的字符串
function getdotstring($string, $vartype='int', $allownull=false, $varscope=array(), $sqlmode=1, $unique=true) {

	if(is_array($string)) {
		$stringarr = $string;
	} else {
		if(substr($string, 0, 1) == '$') {
			return $string;
		}
		$string = str_replace('，', ',', $string);
		$string = str_replace(' ', ',', $string);
		$stringarr = explode(',', $string);
	}

	$newarr = array();
	foreach ($stringarr as $value) {
		$value = trim($value);
		if($vartype == 'int') {
			$value = intval($value);
		}
		if(!empty($varscope)) {
			if(in_array($value, $varscope)) {
				$newarr[] = $value;
			}
		} else {
			if($allownull) {
				$newarr[] = $value;
			} else {
				if(!empty($value)) $newarr[] = $value;
			}
		}
	}

	if($unique) $newarr = sarray_unique($newarr);

	if($vartype == 'int') {
		$string = implode(',', $newarr);
	} else {
		if($sqlmode) {
			$string = '\''.implode('\',\'', $newarr).'\'';
		} else {
			$string = implode(',', $newarr);
		}
	}
	return $string;
}
//将数组中相同的值去掉,同时将后面的键名也忽略掉
function sarray_unique($array) {
	$newarray = array();
	if(!empty($array) && is_array($array)) {
		$array = array_unique($array);
		foreach ($array as $value) {
			$newarray[] = $value;
		}
	}
	return $newarray;
}
/**
 * 截取字符串,多编码
 *
 * @param string $content	原字符串
 * @param string $length	截取长度
 * @param string $num		0=字节  1=个数
 * @param string $add		结尾添加 '..'
 * @param string $code		编码 utf-8或其他
 * @return string
 */
function substrs($content,$length,$num=0,$add=0,$code=''){
	$code = $code ? $code : CHARSET;
	$content = strip_tags($content);
	if($length && strlen($content)>$length){
		$retstr='';
		if($code == 'UTF-8'){
			$retstr = utf8_trim($content,$length,$num);
		}else{
			for($i = 0; $i < $length; $i++) {
				if(ord($content[$i]) > 127){
					if($num){
						$retstr .=$content[$i].$content[$i+1];
						$i++;
						$length++;
					}elseif(($i+1<$length)){
						$retstr .=$content[$i].$content[$i+1];
						$i++;
					}
				}else{
					$retstr .=$content[$i];
				}
			}
		}
		return $retstr.($add ? '..' : '');
	}
	return $content;
}

function utf8_trim($string,$length,$num) {
	if($length && strlen($string)>$length){
		if($num){
			$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
			preg_match_all($pa, $string, $t_string);
			return join('', array_slice($t_string[0], 0, $length));
		}else{
			$hex = '';
			$str = substr($string,0,$length);
			for($i=$length-1;$i>=0;$i--){
				$ch   = ord($str[$i]);
				$hex .= ' '.$ch;
				if(($ch & 128)==0)	return substr($str,0,$i);
				if(($ch & 192)==192)return substr($str,0,$i);
			}
			return($str.$hex);
		}
	}
	return $string;
}


//--------------------------------
//检测file_put_contents是否存在
//
//--------------------------------
if(!function_exists('file_put_contents'))
{
	define('FILE_APPEND', 8);
	function file_put_contents($file, $data, $append = '')
	{
		$mode = $append == '' ? 'wb' : 'ab';
		$fp = @fopen($file, $mode) or exit("Can not open file $file !");
		flock($fp, LOCK_EX);
		$len = @fwrite($fp, $data);
		flock($fp, LOCK_UN);
		@fclose($fp);
		return $len;
	}
}
if(!function_exists('http_build_query'))
{
    function http_build_query($data, $prefix = null, $sep = '', $key = '')
	{
        $ret = array();
		foreach((array)$data as $k => $v)
		{
			$k = urlencode($k);
			if(is_int($k) && $prefix != null)
			{
				$k = $prefix.$k;
			}
			if(!empty($key)) {
				$k = $key."[".$k."]";
			}
			if(is_array($v) || is_object($v))
			{
				array_push($ret,http_build_query($v,"",$sep,$k));
			}
			else
			{
				array_push($ret,$k."=".urlencode($v));
			}
		}
        if(empty($sep))
		{
            $sep = ini_get("arg_separator.output");
        }
        return implode($sep, $ret);
    }
}

if(!function_exists('json_encode'))
{
	function json_encode($string)
	{
		require_once 'json.class.php';
		$json = new json();
		return $json->encode($string);
	}
}

if(!function_exists('json_decode'))
{
	function json_decode($string,$type = 1)
	{
		require_once 'json.class.php';
		$json = new json();
		return $json->decode($string,$type);
	}
}

if(!function_exists('iconv'))
{
	function iconv($in_charset, $out_charset, $str)
	{
		if(function_exists('mb_convert_encoding'))
		{
			return mb_convert_encoding($str, $out_charset, $in_charset);
		}
		else
		{

			require_once 'iconv.func.php';
			$in_charset = strtoupper($in_charset);
			$out_charset = strtoupper($out_charset);
			if($in_charset == 'UTF-8' && ($out_charset == 'GBK' || $out_charset == 'GB2312'))
			{
				return utf8_to_gbk($str);
			}
			if(($in_charset == 'GBK' || $in_charset == 'GB2312') && $out_charset == 'UTF-8')
			{
				return gbk_to_utf8($str);
			}
			return $str;
		}
	}
}


/**
 * 创建多级目录。
 *
 * @param string $path  目标路径
 * @return 成功或失败
 */
function createDir($path) {
    $path = str_replace('\\','/',$path) ;
    if ( is_dir($path) ) return true ;
    if ( file_exists($path) ) return false ;        
    $parent = substr($path ,0, strrpos($path,'/') ) ;
    //echo $parent;
    if ( $parent==='' || $parent==='.' || createDir( $parent ) ) 
         return @mkdir($path) ;        #    没权限的
    else return false ;
}
    
//createDir('/a/b/c/d/e/f') ;     #    从根目录
//createDir('./a/b/c/d/e/f') ;    #    从当前目录
//createDir('a/b/c/d/e/f') ;    #    从当前目录
//createDir('../a/b/c/d/e/f') ;    #    从上级目录
//--------------------------------
//检测获取缓存写入文件
//
//--------------------------------
function createhtml($file)
{
	$data = ob_get_contents();
	ob_clean();
	createDir(dirname($file));
	$strlen = file_put_contents($file, $data);
	@chmod($file,0777);
	return $strlen;
}
/*
*写日志文件 记录重要操作
**/

function writelog($file, $log) {
	global $timestamp;
	$yearmonth = date('Ym');
	$logdir = ROOT_PATH.'/data/logs/';
	$logfile = $logdir.$yearmonth.'_'.$file.'.php';
	if(@filesize($logfile) > 2084000) {
		$dir = opendir($logdir);
		$length = strlen($file);
		$maxid = $id = 0;
		while($entry = readdir($dir)) {
			if(strexists($entry, $yearmonth.'_'.$file)) {
				$id = intval(substr($entry, $length + 8, -4));
				$id > $maxid && $maxid = $id;
			}
		}
		closedir($dir);

		$logfilebak = $logdir.$yearmonth.'_'.$file.'_'.($maxid + 1).'.php';
		@rename($logfile, $logfilebak);
	}
	if($fp = @fopen($logfile, 'a')) {
		@flock($fp, 2);
		$log = is_array($log) ? $log : array($log);
		foreach($log as $tmp) {
			fwrite($fp, "<?PHP exit;?>\t".str_replace(array('<?', '?>'), '', $tmp)."\n");
		}
		fclose($fp);
	}
}
//判断字符是否存在另一个字符串里面 没返回false
function strexists($haystack, $needle) {
	return !(strpos($haystack, $needle) === FALSE);
}
//处理text文本域
function format_textarea($string)
{	
	$string = stripslashes($string);
	return addslashes(nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($string))));
}
//生成随机字符串
function randomkeys($length,$type="ALL")
{	$key="";
	if ($type=="ALL") {
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyz';    //字符池
	}elseif ($type=="123"){
		$pattern = '123456789012345678901234567890123456';    //字符池
	}elseif ($type="ABC"){
		$pattern = 'abcdefghijklmnopqrstuvwxyzabcdefghij';    //字符池
	}
	for($i=0;$i<$length;$i++)
	{
		$key .= $pattern{mt_rand(0,35)};    //生成php随机数
	}
	return $key;
}

function AjaxHead()
{
	@header("Pragma:no-cache\r\n");
	@header("Cache-Control:no-cache\r\n");
	@header("Expires:0\r\n");
}
//加密cookie用
/**  
03 * @param string $string 原文或者密文  
04 * @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE  
05 * @param string $key 密钥  
06 * @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效  
07 * @return string 处理后的 原文或者 经过 base64_encode 处理后的密文  
09 * @example  
11 *  $a = authcode('abc', 'ENCODE', 'key');  
12 *  $b = authcode($a, 'DECODE', 'key');  // $b(abc)  
14 *  $a = authcode('abc', 'ENCODE', 'key', 3600);  
15 *  $b = authcode('abc', 'DECODE', 'key'); // 在一个小时内，$b(abc)，否则 $b 为空  
16 */ 

function get_system_env( )
{
	$e = array();
	$e['time'] = gmdate( "Y-m-d", time( ) );
	$e['os'] = PHP_OS;
	$e['ip'] = @gethostbyname($_SERVER['SERVER_NAME']) or ($e['ip'] = getenv( "SERVER_ADDR" )) or ($e['ip'] = getenv('LOCAL_ADDR'));
	$e['sapi'] = @php_sapi_name( );
	$e['host'] = strtolower(getenv('HTTP_HOST') ? getenv('HTTP_HOST') : $_SERVER['HTTP_HOST']);
	$e['path'] = substr(dirname(__FILE__),0,-17);
	$e['cpu'] = $_ENV['PROCESSOR_IDENTIFIER']."/".$_ENV['PROCESSOR_REVISION'];
	$e['name'] = $_ENV['COMPUTERNAME'];
	return $e;
}
/**
* @package     BugFree
* @version     $Id: FunctionsMain.inc.php,v 1.32 2005/09/24 11:38:37 wwccss Exp $
*
*
* Sort an two-dimension array by some level two items use array_multisort() function.
*
* sysSortArray($Array,”Key1″,”SORT_ASC”,”SORT_RETULAR”,”Key2″……)
* @author                      Chunsheng Wang <wwccss@263.net>
* @param  array   $ArrayData   the array to sort.
* @param  string  $KeyName1    the first item to sort by.
* @param  string  $SortOrder1  the order to sort by(“SORT_ASC”|”SORT_DESC”)
* @param  string  $SortType1   the sort type(“SORT_REGULAR”|”SORT_NUMERIC”|”SORT_STRING”)
* @return array                sorted array.
*/
function sysSortArray($ArrayData,$KeyName1,$SortOrder1 = "SORT_ASC",$SortType1 = "SORT_REGULAR")
{
    if(!is_array($ArrayData))
    {
        return $ArrayData;
    }
 
    // Get args number.
    $ArgCount = func_num_args();
 
    // Get keys to sort by and put them to SortRule array.
    for($I = 1;$I < $ArgCount;$I ++)
    {
        $Arg = func_get_arg($I);
        if(!eregi("SORT",$Arg))
        {
            $KeyNameList[] = $Arg;
            $SortRule[]    = "$".$Arg;
        }
        else
        {
            $SortRule[]    = $Arg;
        }
    }
 
    // Get the values according to the keys and put them to array.
    foreach($ArrayData AS $Key => $Info)
    {
        foreach($KeyNameList AS $KeyName)
        {
            ${$KeyName}[$Key] = $Info[$KeyName];
        }
    }
 
    // Create the eval string and eval it.
    $EvalString = 'array_multisort('.join(",",$SortRule).',$ArrayData);'; 
    eval($EvalString);
    return $ArrayData;
}
?>