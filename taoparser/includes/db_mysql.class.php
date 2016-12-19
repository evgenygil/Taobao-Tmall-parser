<?php
class db_mysql {

	var $version = '';
	var $querynum = 0;
	var $link;
	var $tablepre='';
/**
* 建立数据库连接
* @param string $dbhost
* @param string $dbuser
* @param string $dbpw
* @param string $dbname
* @param int $pconnect
* @param bool $halt
*/

	function connect($dbhost, $dbuser, $dbpass, $dbname = '', $pconnect = 0, $halt = TRUE) {
		global $tablepre;
		$this->tablepre=$tablepre;
		$func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';
		if(!$this->link = @$func($dbhost, $dbuser, $dbpass)) {
			$halt && $this->halt('Can not connect to MySQL server');
		} else {
			if($this->version() > '4.1') {
				global $charset, $dbcharset;
				$dbcharset = !$dbcharset && in_array(strtolower($charset), array('gbk', 'big5', 'utf-8')) ? str_replace('-', '', $charset) : $dbcharset;
				$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
				$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
				$serverset && mysql_query("SET $serverset", $this->link);
			}
			$dbname && @mysql_select_db($dbname, $this->link);
		}

	}
	
	function table_name($tablename) {
		return $this->tablepre.$tablename;
	}
/** 
 * 016. * * 选择数据库 
 * 017.* @param string $dbname 数据库名 
 * 018.* @return bool 019.
 * */
	function select_db($dbname) {
		return mysql_select_db($dbname, $this->link);
	}
/** 
 * 025.* 获取查询的数组 
 * 026.* @param object $query 
 * 027.* @param string $result_type MYSQL_ASSOC 只得到关联索引,MYSQL_NUM 只得到数字索引 
 * 028.* @return array 
 * 029.*/
	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	}
/** 
 * 025.* 获取查询的第一条记录 
 * 026.* @param object $sql 
 * 028.* @return array 
 * 029.*/
	function fetch_first($sql) {
		return $this->fetch_array($this->query($sql));
	}

	function result_first($sql) {
		return $this->result($this->query($sql), 0);
	}
	
	/** 
	 * 035.* 执行一条sql语句 
	 * 036.* @param string $sql 要执行的sql语句 
	 * 037.* @param string $type 如果$type为UNBUFFERED:则执行mysql_unbuffered_query();他与mysql_query的区别是，执行后不获取和缓存结果的行 
	 * 038.* @return object 
	 * 039.
	 * */
	function query($sql, $type = '') {
		$sql=CheckSql($sql);//检查安全过滤sql
		
		$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ?
			'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $func($sql, $this->link))) {
			if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
				$this->close();
				//require ROOT_PATH.'/config.inc.php';
				$this->connect($dbhost, $dbuser, $dbpass, $dbname, $pconnect);
				$this->query($sql, 'RETRY'.$type);
			} elseif($type != 'SILENT' && substr($type, 5) != 'SILENT') {
				$this->halt('MySQL Query Error', $sql);
			}
		}

		$this->querynum++;
		return $query;
	}
/**
* 取得前一次 MySQL 操作所影响的记录行数
* @return int
*/

	function affected_rows() {
		return mysql_affected_rows($this->link);
	}
/**
* 返回上一个 MySQL 操作产生的文本错误信息
* @return string
*/

	function error() {
		return (($this->link) ? mysql_error($this->link) : mysql_error());
	}
/**
* 返回上一个 MySQL 操作中的错误信息的数字编码
* @return int
*/

	function errno() {
		return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
	}
/**
* 取指定的结果集
* @param object $query
* @param int $row
* @return string
*/

	function result($query, $row) {
		$query = @mysql_result($query, $row);
		return $query;
	}
/**
* 取得结果集的行数
* @param object $query
* @return int
*/

	function num_rows($query) {
		$query = mysql_num_rows($query);
		return $query;
	}
/**
* 取得字段数
* @param object $query
* @return int
*/

	function num_fields($query) {
		return mysql_num_fields($query);
	}
/**
* 释放结果内存
* @param object $query
* @return bool
*/

	function free_result($query) {
		return mysql_free_result($query);
	}
/**
* 取得上一步 INSERT 操作产生的 ID
* @return int
*/

	function insert_id() {
		return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}
/**
* 从查询语句中取一条记录
* @param object $query
* @return array
*/

	function fetch_row($query) {
		$query = mysql_fetch_row($query);
		return $query;
	}
/**
* 从结果集中取得列信息
* @param object $query
* @return object
*/

	function fetch_fields($query) {
		return mysql_fetch_field($query);
	}
/**
* 取得 MySQL 服务器信息
* @return string
*/

	function version() {
		if(empty($this->version)) {
			$this->version = mysql_get_server_info($this->link);
		}
		return $this->version;
	}
/**
* 关闭 MySQL 连接
* @return bool
*/

	function close() {
		return mysql_close($this->link);
	}
/**
* 执行错误时，弹出的信息窗口
* @param string $message
* @param string $sql
*/

	function halt($message = '', $sql = '') {
		$dberror = $this->error();
		$dberrno = $this->errno();
		$phperror = '<table style="font-size:11px" cellpadding="0"><tr><td width="270">File</td><td width="80">Line</td><td>Function</td></tr>';
		foreach (debug_backtrace() as $error) {
			$error['file'] = str_replace(str_replace('/',"\\" , ROOT_PATH), '', $error['file']);
			$error['class'] = isset($error['class']) ? $error['class'] : '';
			$error['type'] = isset($error['type']) ? $error['type'] : '';
			$error['function'] = isset($error['function']) ? $error['function'] : '';
			$phperror .= "<tr><td>$error[file]</td><td>$error[line]</td><td>$error[class]$error[type]$error[function]()</td></tr>";
		}
		$phperror .= '</table>';
		echo "<br/>";
		echo '<div style="position:absolute;font-size:11px;font-family:verdana,arial;background:#EBEBEB;padding:0.5em;line-height:1.5em">';
		echo 'SQL Error:<br />'.$message.'<br />'.$sql."<br/>";
		echo $dberror."<br/>";
		echo "<b>PHP Backtrace</b><br />$phperror<br /></div>";
		exit();
	}
	
}

	//SQL语句过滤程序
	function CheckSql($db_string,$querytype='select')
	{
		$clean = '';
		$error='';
		$old_pos = 0;
		$pos = -1;
		$log_file = ROOT_PATH.'/data/sql_safe.txt';
		if(function_exists('real_ip'))$userIP = real_ip();else $userIP="";
		$getUrl = geturl();
	
		//如果是普通查询语句，直接过滤一些特殊语法
		if($querytype=='select')
		{
			$notallow1 = "[^0-9a-z@\._-]{1,}(union|sleep|benchmark|load_file|outfile)[^0-9a-z@\.-]{1,}";
	
			//$notallow2 = "--|/\*";
			if(eregi($notallow1,$db_string))
			{
				fputs(fopen($log_file,'a+'),"$userIP||$getUrl||$db_string||SelectBreak\r\n");
				exit("<font size='5' color='red'>Safe Alert: Request Error step 1 !</font>");
			}
		}
	
		//完整的SQL检查
		while (true)
		{
			$pos = strpos($db_string, '\'', $pos + 1);
			if ($pos === false)
			{
				break;
			}
			$clean .= substr($db_string, $old_pos, $pos - $old_pos);
			while (true)
			{
				$pos1 = strpos($db_string, '\'', $pos + 1);
				$pos2 = strpos($db_string, '\\', $pos + 1);
				if ($pos1 === false)
				{
					break;
				}
				elseif ($pos2 == false || $pos2 > $pos1)
				{
					$pos = $pos1;
					break;
				}
				$pos = $pos2 + 1;
			}
			$clean .= '$s$';
			$old_pos = $pos + 1;
		}
		$clean .= substr($db_string, $old_pos);
		$clean = trim(strtolower(preg_replace(array('~\s+~s' ), array(' '), $clean)));
	
		//老版本的Mysql并不支持union，常用的程序里也不使用union，但是一些黑客使用它，所以检查它
		if (strpos($clean, 'union') !== false && preg_match('~(^|[^a-z])union($|[^[a-z])~s', $clean) != 0)
		{
			$fail = true;
			$error="union detect";
		}
	
		//发布版本的程序可能比较少包括--,#这样的注释，但是黑客经常使用它们
		elseif (strpos($clean, '/*') > 2 || strpos($clean, '--') !== false || strpos($clean, '#') !== false)
		{
			$fail = true;
			$error="comment detect";
		}
	
		//这些函数不会被使用，但是黑客会用它来操作文件，down掉数据库
		elseif (strpos($clean, 'sleep') !== false && preg_match('~(^|[^a-z])sleep($|[^[a-z])~s', $clean) != 0)
		{
			$fail = true;
			$error="slown down detect";
		}
		elseif (strpos($clean, 'benchmark') !== false && preg_match('~(^|[^a-z])benchmark($|[^[a-z])~s', $clean) != 0)
		{
			$fail = true;
			$error="slown down detect";
		}
		elseif (strpos($clean, 'load_file') !== false && preg_match('~(^|[^a-z])load_file($|[^[a-z])~s', $clean) != 0)
		{
			$fail = true;
			$error="file fun detect";
		}
		elseif (strpos($clean, 'into outfile') !== false && preg_match('~(^|[^a-z])into\s+outfile($|[^[a-z])~s', $clean) != 0)
		{
			$fail = true;
			$error="file fun detect";
		}
	
		//老版本的MYSQL不支持子查询，我们的程序里可能也用得少，但是黑客可以使用它来查询数据库敏感信息
		elseif (preg_match('~\([^)]*?select~s', $clean) != 0)
		{
			$fail = true;
			$error="sub select detect";
		}
		if (!empty($fail))
		{
			fputs(fopen($log_file,'a+'),"$userIP||$getUrl||$db_string||$error\r\n");
			exit("<font size='5' color='red'>Safe Alert: Request Error step 2!</font>");
		}
		else
		{
			return $db_string;
		}
	}
	
class DB
{

	function table($table) {
		$a = & DB::object();
		return $a->table_name($table);
	}

	function delete($table, $condition, $limit = 0, $unbuffered = true) {
		if(empty($condition)) {
			$where = '1';
		} elseif(is_array($condition)) {
			$where = DB::implode_field_value($condition, ' AND ');
		} else {
			$where = $condition;
		}
		$sql = "DELETE FROM ".DB::table($table)." WHERE $where ".($limit ? "LIMIT $limit" : '');
		return DB::query($sql, ($unbuffered ? 'UNBUFFERED' : ''));
	}

	function insert($table, $data, $return_insert_id = false, $replace = false, $silent = false) {

		$sql = DB::implode_field_value($data);

		$cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';

		$table = DB::table($table);
		$silent = $silent ? 'SILENT' : '';

		$return = DB::query("$cmd $table SET $sql", $silent);

		return $return_insert_id ? DB::insert_id() : $return;

	}

	function update($table, $data, $condition, $unbuffered = false, $low_priority = false) {
		$sql = DB::implode_field_value($data);
		$cmd = "UPDATE ".($low_priority ? 'LOW_PRIORITY' : '');
		$table = DB::table($table);
		$where = '';
		if(empty($condition)) {
			$where = '1';
		} elseif(is_array($condition)) {
			$where = DB::implode_field_value($condition, ' AND ');
		} else {
			$where = $condition;
		}
		$res = DB::query("$cmd $table SET $sql WHERE $where", $unbuffered ? 'UNBUFFERED' : '');
		return $res;
	}

	function implode_field_value($array, $glue = ',') {
		$sql = $comma = '';
		foreach ($array as $k => $v) {
			$sql .= $comma."`$k`='$v'";
			$comma = $glue;
		}
		return $sql;
	}

	function insert_id() {
		$db = & DB::object();
		return $db->insert_id();
	}

	function fetch($resourceid) {
		$db = & DB::object();
		return $db->fetch_array($resourceid);
	}

	function fetch_first($sql) {
		$db = & DB::object();
		return $db->fetch_first($sql);
	}

	function result($resourceid, $row = 0) {
		$db = & DB::object();
		return $db->result($resourceid, $row);
	}

	function result_first($sql) {
		$db = & DB::object();
		return $db->result_first($sql);
	}

	function query($sql, $type = '') {
		$db = & DB::object();
		return $db->query($sql, $type);
	}

	function num_rows($resourceid) {
		$db = & DB::object();
		return $db->num_rows($resourceid);
	}

	function affected_rows() {
		$db = & DB::object();
		return $db->affected_rows();
	}

	function free_result($query) {
		$db = & DB::object();
		return $db->free_result($query);
	}

	function error() {
		$db = & DB::object();
		return $db->error();
	}

	function errno() {
		$db = & DB::object();
		return $db->errno();
	}

	function &object() {
		static $db;
		if(empty($db)) {
			$db = new db_mysql();
		}
		return $db;
	}
}
?>