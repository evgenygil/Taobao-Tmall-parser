<?php
//时间设置
//error_reporting(E_ALL ^ E_NOTICE);//错误报告级别
error_reporting(0);//错误报告级别
ini_set('display_errors',0);
//error_reporting(E_ERROR | E_PARSE);//错误报告级别
//set_magic_quotes_runtime(0);
define('KEY',"zzqss");
define('ZZQSS',true);
define('ROOT_PATH', str_replace("\\", '/', dirname(__FILE__)));
$php_self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
define('PHP_SELF',  htmlentities($php_self));
define('ROOT_DIR',  substr(PHP_SELF, 0, strrpos(PHP_SELF, '/')));
define('SITE_URL', "http://".$_SERVER['HTTP_HOST'].ROOT_DIR);
define('INC_PATH', 'includes');
$site_url=SITE_URL;
//模板缓存目录
define('TPL_CACHEPATH', '/data/cache_template');
//系统设置变量缓存目录
define('CFG_CACHEPATH', '/data/syscache');
define('DATA_CACHEPATH', '/data/datacache/');

include_once( '../config.php');

	$database = 'mysql';
	$dbcharset = 'utf8';
	$charset = 'utf-8';	
	define('CHARSET', 'UTF-8');
	
include_once( 'includes/fun_common.php');

require_once 'includes/db_mysql.class.php';
$db=& DB::object();
$db->connect($dbhost, $dbuser, $dbpass, $dbname, $pconnect);
include_once(INC_PATH."/table.class.php");//数据表操作基础类


?>