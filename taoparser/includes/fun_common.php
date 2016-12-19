<?php


require_once(INC_PATH.'/fun_base.php');//包含共用基础函数
//php跳转
function dheader($string, $replace = true, $http_response_code = 0) {
	$string = str_replace(array("\r", "\n"), array('', ''), $string);
	if(!preg_match('/^\s*location:/is', $string)) {
		$string = "Location: ".$string;
	}
	if(empty($http_response_code) || PHP_VERSION < '4.3' ) {
		@header($string, $replace);
	} else {
		@header($string, $replace, $http_response_code);
	}
	if(preg_match('/^\s*location:/is', $string)) {
		exit;
	}
}
//简单跳转的函数
function jumpurl($url, $time=1000, $mode='js') {
	if($mode == 'js') {
		echo "<script>
			function redirect() {
				window.location.replace('$url');
			}
			setTimeout('redirect();', $time);
			</script>";
	} else {
		$time = $time/1000;
		echo "<html><head><title></title><meta http-equiv=\"refresh\" content=\"$time;url=$url\"></head><body></body></html>";
	}
	exit;
}
//下载成excil格式

function down_xls($data, $keynames, $name='dataxls') {
	$xls[] = implode("\t", array_values($keynames));
	foreach($data As $o) {
		$line = array();
		foreach($keynames AS $k=>$v) {
		if($k=="endtime")$o[$k]=date("Y-m-d H:i",$o[$k]);
			$line[] = $o[$k];
		}
		$xls[] = implode("\t", $line);
	}
	$xls = join("\n", $xls);
	header('Content-Disposition: attachment; filename="'.$name.'.xls"');
	die(iconv(CHARSET,'GBK',$xls));
}

//生成下拉框
function getselectstr($var, $optionarray, $value='', $other='') {
	$selectstr = '<select id="'.$var.'" name="'.$var.'">';
	$selectstr .= '<option value="">'.$other.'</option>';
	foreach ($optionarray as $optionkey => $optionvalue) {
		$selectstr .= '<option value="'.$optionkey.'">'.$optionvalue.'</option>';
	}
	if($value!=""){
			$selectstr = str_replace('value="'.$value.'"', 'value="'.$value.'" selected', $selectstr);
	}
	$selectstr .= '</select>';
	return $selectstr;
}
//日期时间格式化
function ddate($format, $timestamp, $convert = 1) {
	$s = date($format, $timestamp);
	if($convert) {
		$time = time() - $timestamp;
		if($timestamp <=  time() && $time <=24*3600) {
			if($time >= 3600) {
				return '<span title="'.$s.'">'.intval($time / 3600).'&nbsp;'.lang('Hours_ago').'</span>';
			} elseif($time >= 1800) {
				return '<span title="'.$s.'">'.lang('Half_hours_ago').'</span>';
			} elseif($time >= 60) {
				return '<span title="'.$s.'">'.intval($time / 60).'&nbsp;'.lang('Minutes_ago').'</span>';
			} elseif($time > 0) {
				return '<span title="'.$s.'">'.$time.'&nbsp;'.lang('Seconds_ago').'</span>';
			} elseif($time == 0) {
				return '<span title="'.$s.'">'.lang('Just').'</span>';
			} else {
				return '<span title="'.$s.'">'.$s.'</span>';
			}
		} else {
			return '<span title="'.$s.'">'.$s.'</span>';
		}
	} else {
		return '<span title="'.$s.'">'.$s.'</span>';
	}
}

function showmessage($msg,$gourl="",$type=true,$limittime=4000){
	global $cfg_site_logo,$cfg_site_url,$cfg_site_bottomlogo,$cfg_site_name,$cfg_groupbond_name,$cfg_referrals_money,$_USERS;//声明全局变量
	if ($type) {
		$css="success";
		$tip=lang('Operation_success');
	}else {
		$css="error";
		$tip==lang('Operation_failed');
	}
	if (empty($gourl)) {
		$gourl="index.php";
	}
	if($limittime==0) $litime = 5000;
	else $litime=$limittime;
	if($gourl=="-1"){
			$gourl = "javascript:history.go(-1);";
	}
	$func = "<script>      var pgo=0;
      function JumpUrl(){
        if(pgo==0){ location='$gourl'; pgo=1; }
      }\r\n";
	$func .= "setTimeout('JumpUrl()',$litime)</script>";
	
	$msg.=$func;
	include_once(template('showmessage'));//包含输出指定模板
	exit;
}


/**
 *提示信息显示！showmsg();
 *
 * @$msg  信息
 */
function showmsg($msg,$gourl,$onlymsg=0,$limittime=0)
{
	    $cfg_ver_lang = CHARSET;
		$htmlhead  = "<html>\r\n<head>\r\n<title>".lang('Prompted')."</title>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".CHARSET."\" />\r\n";
		$htmlhead .= "<base target='_self'/>\r\n</head>\r\n<body leftmargin='0' topmargin='0'>\r\n<center>\r\n<script>\r\n";
		$htmlfoot  = "</script>\r\n</center>\r\n</body>\r\n</html>\r\n";

		if($limittime==0) $litime = 5000;
		else $litime = $limittime;

		if($gourl=="-1"){
			if($limittime==0) $litime = 5000;
			$gourl = "javascript:history.go(-1);";
		}

		if($gourl==""||$onlymsg==1){
			$msg = "<script>alert(\"".str_replace("\"","“",$msg)."\");</script>";
		}else{
			$func = "      var pgo=0;
      function JumpUrl(){
        if(pgo==0){ location='$gourl'; pgo=1; }
      }\r\n";
			$rmsg = $func;
			$rmsg .= "document.write(\"<br/><div style='width:400px;padding-top:4px;height:24;font-size:10pt;border:1px solid #ccc; background-color:#F5F5F5'>".lang('TipsMessage')."</div>\");\r\n";
			$rmsg .= "document.write(\"<div style='width:400px;height:100;font-size:10pt;border:1px solid #ccc; border-top:0px; background-color:#fff'><br/><br/>\");\r\n";
			$rmsg .= "document.write(\"".str_replace("\"","“",$msg)."\");\r\n";
			$rmsg .= "document.write(\"";
			if($onlymsg==0){
				if($gourl!="javascript:;" && $gourl!=""){ $rmsg .= "<br/><br/><a href='".$gourl."'>".lang('browser_notrespond_click')."</a>"; }
				$rmsg .= "<br/><br/></div>\");\r\n";
				if($gourl!="javascript:;" && $gourl!=""){ $rmsg .= "setTimeout('JumpUrl()',$litime);"; }
			}else{ $rmsg .= "<br/><br/></div>\");\r\n"; }
			$msg  = $htmlhead.$rmsg.$htmlfoot;
		}
	
		exit;
}







/**
 *分页函数dz！multi();
 *$num 总页数
 *$perpage 一页显示多少
 * $curpage 当前页码
 *$mpurl url地址
 *$page 页码数量
 * 
 */

function pagelist($num, $perpage, $curpage, $mpurl, $maxpages = 0, $page = 10, $autogoto = TRUE, $simple = FALSE) {
global $maxpage;
$ajaxtarget = !empty($_GET['ajaxtarget']) ? " ajaxtarget=\"".dhtmlspecialchars($_GET['ajaxtarget'])."\" " : '';

$multipage = '';
if(!$mpurl){ $mpurl=$_SERVER["REQUEST_URI"];}
//URL分析：
$parse_url=parse_url($mpurl);
$url_query=$parse_url["query"]; //单独取出URL的查询字串
if($url_query){
//因为URL中可能包含了页码信息，我们要把它去掉，以便加入新的页码信息。
$url_query=ereg_replace("(^|&)page=$curpage","",$url_query);
//将处理后的URL的查询字串替换原来的URL的查询字串：
$mpurl=str_replace($parse_url["query"],$url_query,$mpurl);
}
$mpurl .= strpos($mpurl, '?') ? '&amp;' : '?';
$realpages = 1;
if($num > $perpage) {
   $offset = 2;

   $realpages = @ceil($num / $perpage);
   $pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;

   if($page > $pages) {
    $from = 1;
    $to = $pages;
   } else {
    $from = $curpage - $offset;
    $to = $from + $page - 1;
    if($from < 1) {
     $to = $curpage + 1 - $from;
     $from = 1;
     if($to - $from < $page) {
      $to = $page;
     }
    } elseif($to > $pages) {
     $from = $pages - $page + 1;
     $to = $pages;
    }
   }

   $multipage = ($curpage - $offset > 1 && $pages > $page ? '<li><a href="'.$mpurl.'page=1" class="first"'.$ajaxtarget.'>1 ...</a></li>' : '').
    ($curpage > 1 && !$simple ? '<li><a href="'.$mpurl.'page='.($curpage - 1).'" class="prev"'.$ajaxtarget.'>'.lang('onpage').'</a></li>' : '');
   for($i = $from; $i <= $to; $i++) {
    $multipage .= $i == $curpage ? '<li class="current">'.$i.'</li>' :
     '<li><a href="'.$mpurl.'page='.$i.($ajaxtarget && $i == $pages && $autogoto ? '#' : '').'"'.$ajaxtarget.'>'.$i.'</a></li>';
   }

   $multipage .= ($curpage < $pages && !$simple ? '<li><a href="'.$mpurl.'page='.($curpage + 1).'" class="next"'.$ajaxtarget.'>'.lang('nextpage').'</a></li>' : '').
    ($to < $pages ? '<li><a href="'.$mpurl.'page='.$pages.'" class="last"'.$ajaxtarget.'>... '.$realpages.'</a></li>' : '').
    (!$simple && $pages > $page && !$ajaxtarget ? '<kbd><input type="text" name="custompage" size="3" onkeydown="if(event.keyCode==13) {window.location=\''.$mpurl.'page=\'+this.value; return false;}" /></kbd>' : '');

   $multipage = $multipage ? '<div class="pages">'.(!$simple ? '<em>'.lang('Total').' &nbsp;'.$num.'&nbsp; '.lang('Article').'</em>' : '').$multipage.'</div>' : '';
}
$maxpage = $realpages;
return $multipage;
}


//添加数据
function inserttable($tablename, $insertsqlarr, $returnid=0, $replace = false, $silent=0) {
	global $db,$tablepre;
	if(!strexists($tablename,$tablepre))$tablename=$tablepre.$tablename;
	$insertkeysql = $insertvaluesql = $comma = '';
	foreach ($insertsqlarr as $insert_key => $insert_value) {
		$insertkeysql .= $comma.'`'.$insert_key.'`';
		$insertvaluesql .= $comma.'\''.$insert_value.'\'';
		$comma = ', ';
	}
	$method = $replace?'REPLACE':'INSERT';
	$db->query($method.' INTO '.$tablename.' ('.$insertkeysql.') VALUES ('.$insertvaluesql.')', $silent?'SILENT':'');
	if($returnid && !$replace) {
		return $db->insert_id();
	}
}
//更新数据
function updatetable($tablename, $setsqlarr, $wheresqlarr, $silent=0) {
	global $db,$tablepre;
	if(!strexists($tablename,$tablepre))$tablename=$tablepre.$tablename;
	$setsql = $comma = '';
	foreach ($setsqlarr as $set_key => $set_value) {
		$setsql .= $comma.'`'.$set_key.'`'.'=\''.$set_value.'\'';
		$comma = ', ';
	}
	$where = $comma = '';
	if(empty($wheresqlarr)) {
		$where = '1';
	} elseif(is_array($wheresqlarr)) {
		foreach ($wheresqlarr as $key => $value) {
			$where .= $comma.'`'.$key.'`'.'=\''.$value.'\'';
			$comma = ' AND ';
		}
	} else {
		$where = $wheresqlarr;
	}
	$db->query('UPDATE '.$tablename.' SET '.$setsql.' WHERE '.$where, $silent?'SILENT':'');
}
//更新统计字段

function addfield($tablename,$field,$wheresqlarr,$num,$r=false){
	global $db,$tablepre;
	if(is_numeric($num) && !empty($field) && !empty($tablename)){
		if(!strexists($tablename,$tablepre))$tablename=$tablepre.$tablename;
		$db->query('UPDATE '.$tablename.' SET '.$field.'='.$field."+".$num.' WHERE '.$wheresqlarr);
		if ($r==true) {
			$count=$db->result_first("Select {$field} From {$tablename} WHERE {$wheresqlarr}");
			return $count;
		}
		//print_r('UPDATE '.$tablename.' SET '.$field.'='.$field."+".$num.' WHERE '.$wheresqlarr);
	}
}
//更新统计字段

function editstate($tablename,$field,$wheresqlarr,$state){
	global $db,$tablepre;
	if(!empty($field) && !empty($tablename)){
		if(!strexists($tablename,$tablepre))$tablename=$tablepre.$tablename;
		$db->query('UPDATE '.$tablename.' SET '.$field.'=\''.$state.'\' WHERE '.$wheresqlarr);
	}
}
/**
 * 清空模板缓存
 *
 */
function cache_page_clear()
{
	@set_time_limit(600);
		$files = glob(TPL_CACHEPATH.'/*');
		foreach($files as $file)
		{	if (is_dir($file)) {
				$files2 = glob($file.'/*');
				foreach($files2 as $file){
					@unlink($file);
				}
			}else {
				@unlink($file);	
			}
			
		}
		$files = glob(DATA_CACHEPATH.'*');
		foreach($files as $file)
		{
			@unlink($file);
		}
}
//根据用户ID查询用户信息
function getuserbyuid($uid) {
	static $users = array();
	if(empty($users[$uid])) {
		$users[$uid] = DB::fetch_first("SELECT * FROM ".DB::table('users')." WHERE uid='$uid'");
	}
	return $users[$uid];
}
//检测用户是否登录
function checkauth(){
	global $db,$tablepre,$_USERS;
	$loginauth=get_cookie('loginauth');
	if(!empty($loginauth)) {
		@list($uid, $uname,$pwd) = explode("\t", cookie_authcode($loginauth,'DECODE'));
		$_USERS['uid'] = intval($uid);
		if(!empty($uid) && !empty($uname) && !empty($pwd) && !empty($_USERS['uid'])){
			$row = getuserbyuid($uid);
			//$row = $db->fetch_first("Select * From {$tablepre}users where uname = '{$uname}' and password='{$pwd}'");//检测数据库
			if(is_array($row) && $row['password']==$pwd){
				$_USERS['uname']=$row['uname'];
				$_USERS['email']=$row['email'];
				$_USERS['password']=$row['password'];
				$_USERS['tel']=$row['tel'];
				$_USERS['scores']=$row['scores'];
				$_USERS['money']=$row['money'];
				$_USERS['tname']=$row['tname'];
				$_USERS['showname']=!empty($row['tname'])?$row['tname']:$row['uname'];//显示名字
				$_USERS['face']=$row['face'];
				$_USERS['logintime']=$row['logintime'];
				$_USERS['pm']=DB::result_first("Select count(mid) From ".DB::table('pm')." where "."touname='".$_USERS['uname']."' and type=1 and writetime!='' and hasview=0");
				$_USERS['state']=$row['state'];
				$_USERS['utype']=$row['utype'];
				$_USERS['validity']=$row['validity'];

			}else $_USERS=array();
		}else $_USERS=array();
	}else $_USERS=array();
}
//检测用户

//检查是否登录
function checklogin(){
	global $_USERS;
	if(empty($_USERS['uid'])) {
		set_cookie('_refer', rawurlencode(remove_xss($_SERVER['REQUEST_URI'])));//设置登录成功跳转地址
		dheader(url("user.php?action=loginandreg"));//php跳转页面
	}elseif (!empty($_USERS) && $_USERS['state']==0){
		dheader(url("user.php?action=useractive"));//php跳转页面
	}
}
//检查站点是否关闭
function checkclose(){
	global $cfg_site_closed;
	if($cfg_site_closed=="Y")
	showmessage(lang('website_closed'),"",false,99999);
}
Class Xxtea {
 
	function encrypt($s,$key,$isArray=false){
                $isArray && $s = serialize($s);
		return str_replace(array('+','/','='), array('-','_','.'), base64_encode($this->xxtea_encrypt($s, $key)));
	}
 
	function decrypt($e,$key,$isArray=false){                
		$c = str_replace(array('-','_','.'), array('+','/','='), $e);
                if($isArray === false){
                    return $this->xxtea_decrypt(base64_decode($c), $key);
                }else{
                    return unserialize($this->xxtea_decrypt(base64_decode($c), $key));
                }
 
	}
 
	function long2str($v, $w) {
		$len = count($v);
		$n = ($len - 1) << 2;
		if ($w) {
			$m = $v[$len - 1];
			if (($m < $n - 3) || ($m > $n)) return false;
			$n = $m;
		}
		$s = array();
		for ($i = 0; $i < $len; $i++) {
			$s[$i] = pack("V", $v[$i]);
		}
		if ($w) {
			return substr(join('', $s), 0, $n);
		} else {
			return join('', $s);
		}
	}
	function str2long($s, $w) {
		$v = unpack("V*", $s. str_repeat("\0", (4 - strlen($s) % 4) & 3));
		$v = array_values($v);
		if ($w) {
			$v[count($v)] = strlen($s);
		}
		return $v;
	}
	function int32($n) {
		while ($n >= 2147483648) $n -= 4294967296;
		while ($n <= -2147483649) $n += 4294967296;
		return (int)$n;
	}
	function xxtea_encrypt($str, $key) {
		if ($str == "") {
			return "";
		}
		$v = $this->str2long($str, true);
		$k = $this->str2long($key, false);
		if (count($k) < 4) {
			for ($i = count($k); $i < 4; $i++) {
				$k[$i] = 0;
			}
		}
		$n = count($v) - 1;
		$z = $v[$n];
		$y = $v[0];
		$delta = 0x9E3779B9;
		$q = floor(6 + 52 / ($n + 1));
		$sum = 0;
 
		while (0 < $q--) {
			$sum = $this->int32($sum + $delta);
			$e = $sum >> 2 & 3;
			for ($p = 0; $p < $n; $p++) {
				$y = $v[$p + 1];
				$mx = $this->int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
				$z = $v[$p] = $this->int32($v[$p] + $mx);
			}
			$y = $v[0];
			$mx = $this->int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$z = $v[$n] = $this->int32($v[$n] + $mx);
		}
		return $this->long2str($v, false);
	}
	function xxtea_decrypt($str, $key) {
		if ($str == "") {
			return "";
		}
		$v = $this->str2long($str, false);
		$k = $this->str2long($key, false);
		if (count($k) < 4) {
			for ($i = count($k); $i < 4; $i++) {
				$k[$i] = 0;
			}
		}
		$n = count($v) - 1;
 
		$z = $v[$n];
		$y = $v[0];
		$delta = 0x9E3779B9;
		$q = floor(6 + 52 / ($n + 1));
		$sum = $this->int32($q * $delta);
 
		while ($sum != 0) {
			$e = $sum >> 2 & 3;
			for ($p = $n; $p > 0; $p--) {
				$z = $v[$p - 1];
				$mx = $this->int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
				$y = $v[$p] = $this->int32($v[$p] - $mx);
			}
			$z = $v[$n];
			$mx = $this->int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$y = $v[0] = $this->int32($v[0] - $mx);
			$sum = $this->int32($sum - $delta);
		}
		return $this->long2str($v, true);
	}
}

function checkkey(){
	global $_CKEY;
	$_CKEY=0;
	$auth_file = ROOT_PATH."/domain_key.dat";
	if (!file_exists( $auth_file ))
	{
		return false;
		exit;
	}
	$str = @file_get_contents($auth_file);
	$xxtea = new Xxtea();
	$jiamicode=pack('H*',$str);
	$strcode=$xxtea->decrypt($jiamicode,"zzqss");
	$strcode=cookie_authcode($strcode,'DECODE',"zzqss");
	$strcode=base64_decode($strcode);
	list($shangye,$domainstr)=explode("@", $strcode);
	if(empty($domainstr)||empty($shangye))return false;
	$strcode=unserialize($domainstr);
	$host = $_SERVER['HTTP_HOST'];
	if (!in_array( $host,$strcode)) {
		return false;
		exit;
	}else{
		$_CKEY=2;
		return true;
	}
}


//获取一个远程图片
function GetRemoteImage($url,$filename="",$filepath="order")
{
	include_once('httpdown.class.php');
	//上传目录按照日期来建立 每月一个
	$savePath="../attachment/{$filepath}/".date("Ym");
	$savePath2="attachment/{$filepath}/".date("Ym").'/'; //存储数据库目录
	$savePath = substr( str_replace("\\","/", $savePath) , -1) == "/" 
	? $savePath : $savePath."/";
	if(!is_dir($savePath))createDir($savePath);//目录不存在就创建一个

	$revalues = Array();
	$ok = false;
	$htd = new HttpDown();
	$htd->OpenUrl($url);
	$sparr = Array("image/pjpeg", "image/jpeg", "image/gif", "image/png", "image/xpng", "image/wbmp");
	if(!in_array($htd->GetHead("content-type"),$sparr))
	{
		return '';
	}
	else
	{
		$itype = $htd->GetHead("content-type");
		if($itype=="image/gif")
		{
			$itype = '.gif';
		}
		else if($itype=="image/png")
		{
			$itype = '.png';
		}
		else if($itype=="image/wbmp")
		{
			$itype = '.bmp';
		}
		else
		{
			$itype = '.jpg';
		}
		if(!empty($filename)){
			$savename=$filename;
		}else{
			$savename = date('YmdHis')."_".rand(100,999).$itype;
			//判断文件是否存在,不允许重复文件
			if(file_exists($savePath . $savename)){
				$savename = date('YmdHis')."_".rand(100,999).$itype;
			}
		}
		$ok = $htd->SaveToBin($savePath . $savename);
		
		if($ok)
		{
			$revalues['filepath']=$savePath2 . $savename;
			$revalues['filename']=$savename;
			$revalues['savepath']=$savePath2;

		}
	}
	$htd->Close();
	return ($ok ? $revalues : '');
}


class url 
{
	static function replace($url, $options) 
	{
		$options = self::optInit($options); 
		$Query = parse_url( $url , PHP_URL_QUERY ); 
		if($Query){ 
			parse_str($Query,$GET); 
		if ($GET){
			//foreach ( $GET as $_k => $_v ){ 
			// //if( array_key_exists( $_k , $options)){ 
			// $GET[$_k] = $options[$_k]; 
			// //} 
			//} 
			$GET = array_merge($GET,$options); 
		}
		return str_replace( $Query , http_build_query($GET),$url); 
		} 
		if( !$Query && $options){
			return $url . "?" . http_build_query($options); 
		} 
		return $url; 
	}
	static private function optInit($options) 
	{
		if(is_string($options)){
			$optlists = explode(',',$options); 
			foreach( $optlists as $val){
				list($tmpKey,$tmpVal) = explode('=',$val);
				$opts[$tmpKey] = $tmpVal; 
			}
		}else{
			$opts = $options; 
		}
	return $opts;
	}
}

function get_thumb($file=""){
	if(!empty($file)){
		$file_part = pathinfo($file);
		return $file_part['dirname']."/_thumb_".$file_part['basename'];
	}else{
		return '';
	}
}

require_once(INC_PATH.'/fun_check.php');//包含字段检测函数
?>