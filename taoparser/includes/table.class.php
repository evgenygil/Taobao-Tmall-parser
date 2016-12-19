<?php


class TableClass {
	var $db;
	var $table;
	var $tablepre;
	var $idname="id";
	function __construct($tablename="",$id="id"){
		//设置全局变量
		global $db,$tablepre;
		$this->db=$db;
		$this->tablepre=$tablepre;
		$this->table=$tablepre.$tablename;
		$this->idname=$id;
	}	
	function TableClass($tablename="",$id="id"){
		$this->__construct($tablename,$id);
	}
	function set($tablename="",$id="id") {
		//设置全局变量
		global $db,$tablepre;
		$this->db=$db;
		$this->tablepre=$tablepre;
		$this->table=$tablepre.$tablename;
		$this->idname=$id;
	}
	/**
	 * 添加信息
	 *
	 * @param unknown_type $garray
	 * @return unknown
	 */
	function add($dataarray){
		if(is_array($dataarray)){
			$oid=inserttable($this->table, $dataarray,1);
			if(is_numeric($oid))return $oid;
			else return lang('insert_data_error');
		}return lang('datatype_error');
	}
	/**
	 * 编辑指定ID信息
	 *
	 * @param unknown_type $eid
	 * @return unknown
	 */	
	function edit($eid,$dataarray){
		if(is_array($dataarray) && !empty($eid)){
				//更新数据库操作
				$wheresqlarr=" ".$this->idname."='{$eid}'";
				updatetable($this->table,$dataarray, $wheresqlarr);
				return "OK";
		}return lang('Array_format_error');
	}
	/**
	 * 删除指定ID信息
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	function del($id,$uname="",$other=""){
		if(is_numeric($id)){
			$wherestr[]=$this->idname." = ".$id;
		}elseif(is_array($id)){
			$ids=getdotstring($id,'int');
			$wherestr[]=$this->idname." in ({$ids})";
		}elseif(is_string($id) && (strexists($id,',') || strexists($id,'|'))){
			if(strexists($id,',')){
				$ids=getdotstring($id,'int');
			}else{
				$ids=getdotstring(explode('|',$ids),'int');
			}
			$wherestr[]=$this->idname." in ({$ids})";
		} else{
			return lang('ID_format_error');	
		}
		if ($uname!="") {
			$wherestr[]="uname = '".$uname."'";
		}
		if($other!=""){
			$wherestr[]=$other;
		}
		$wheresql = implode(' AND ', $wherestr);	//条件汇总
		$this->db->query("delete from {$this->table} where ".$wheresql);
		return "OK";
		
	}
	/**
	 * 获取指定ID相关一条信息
	 *
	 * @param unknown_type $eid
	 * @return unknown
	 */	

	/**
	 * 获取数据数组
	 *
	 * @param string $limit
	 * @param string $where
	 * @param string $orderby
	 * @return array
	 */
	function getdata($limit="",$where="",$orderby="",$field="*"){
		$tempdata=array();
		if(!empty($limit))$limit=" limit $limit ";
		if(!empty($where))$where=" where $where ";
		if(!empty($orderby))$orderby=" order by $orderby ";else $orderby=" order by ".$this->idname." desc";
		$sql="select {$field} from {$this->table}{$where}{$orderby}{$limit}";
		$query =$this->db->query($sql);
		while($value = $this->db->fetch_array($query)) {
			$tempdata[]=$value;
		}
		return $tempdata;
	}
	function getcount($where=""){
	if(!empty($where))$where=" where $where";
	$count= $this->db->result_first("Select count(".$this->idname.") From `$this->table` $where");
	return $count;
	}
}
?>