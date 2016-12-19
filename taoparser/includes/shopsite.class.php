<?php

require_once(INC_PATH.'/httpdown.class.php');//包含共用基础函数
class ShopClass {
	var $db;
	var $table_cart;
	var $tablepre;
	var $http;
	protected $_cookieFileLocation = 'cookie.txt'; 

	function __construct(){
		//设置全局变量
		global $db,$tablepre;
		$this->db=$db;
		$this->tablepre=$tablepre;
		$this->table_shopsite=new TableClass("shopsite","sid");
		$this->http=new HttpDown();
		$this->_cookieFileLocation = dirname(__FILE__).'/cookie.txt'; 
	}
	function ShopClass(){
		$this->__construct();
	}
	//对象获取
	function &init() {
		static $object;
		if(empty($object)) {
			$object = new ShopClass();
		}
		return $object;
	}
	//抓取商品信息
	function get($url){
		$matches=$preg=array();
		$this->http->OpenUrl($url);
			/*$html=file_get_contents($url);
			echo $html;*/
			$browser_id = "Mozilla/6.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2471.2 Safari/537.36";
		    $ip = '178.33.160.243';      
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url); 
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERAGENT, $browser_id);
			curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_REFERER, $ip);
			curl_setopt($curl,CURLOPT_COOKIEJAR,$this->_cookieFileLocation); 
			curl_setopt($curl,CURLOPT_COOKIEFILE,$this->_cookieFileLocation); 
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);			
			$html = curl_exec($curl);
			$err = curl_error($curl);
			//echo $err;
			curl_close($curl);
			if(empty($html))
				$html=file_get_contents($url); 
		
		$preg=$this->getpreg($url); 
		if(empty($html)||($preg==false)) 
		{
		echo "error 443";
		return false;
		}
		
		if(!(strpos($url, "world")))
			$html=iconv($preg['charset'],"utf-8//IGNORE",$html);//编码转换
			  
		$response = iconv("gbk","utf-8//IGNORE",$response);
		echo $response;
		//抓取商品名

		if(empty($preg['preg_goodsname'])){
			$result['goodsname']=$preg['preg_goodsname2'];
		}elseif(!empty($preg['preg_goodsname'])){
			preg_match($preg['preg_goodsname'],$html,$matches);
			$result['goodsname']=$matches['this'];
			if(empty($result['goodsname']) && !empty($preg['preg_goodsname2'])){
				preg_match($preg['preg_goodsname2'],$html,$matches);
				$result['goodsname']=$matches['this'];
				if(empty($result['goodsname']) && !empty($preg['preg_goodsname3'])){
					preg_match($preg['preg_goodsname3'],$html,$matches);
					$result['goodsname']=$matches['this'];
				}	
			}
		}
		//抓取价格
		$matches=array();
		if(empty($preg['preg_goodsprice'])){
			$result['goodsprice']=$preg['preg_goodsprice2'];
		}elseif(!empty($preg['preg_goodsprice'])){
			preg_match($preg['preg_goodsprice'],$html,$matches);
			$result['goodsprice']=$matches['this'];
			if(!is_numeric($result['goodsprice']) && !empty($preg['preg_goodsprice2'])){
				preg_match($preg['preg_goodsprice2'],$html,$matches);
				$result['goodsprice']=$matches['this'];
				if(empty($result['goodsprice']) && !empty($preg['preg_goodsprice3'])){
					preg_match($preg['preg_goodsprice3'],$html,$matches);
					$result['goodsprice']=$matches['this'];
				}
			}
		}
		//抓取运费
		$matches=array();
		if(empty($preg['preg_sendprice'])){
			$result['sendprice']=$preg['preg_sendprice2'];
		}elseif(!empty($preg['preg_sendprice'])){
			preg_match($preg['preg_sendprice'],$html,$matches);
			$result['sendprice']=$matches['this'];
			if(empty($result['sendprice']) && !empty($preg['preg_sendprice2'])){
				preg_match($preg['preg_sendprice2'],$html,$matches);
				$result['sendprice']=$matches['this'];
				if(empty($result['sendprice']) && !empty($preg['preg_sendprice3'])){
					preg_match($preg['preg_sendprice3'],$html,$matches);
					$result['sendprice']=$matches['this'];				
				}
				
			}
		}
		//抓取图片
		$matches=array();
		if(empty($preg['preg_goodsimg'])){
			$result['goodsimg']=$preg['preg_goodsimg2'];
		}elseif(!empty($preg['preg_goodsimg'])){
			preg_match($preg['preg_goodsimg'],$html,$matches);
			$result['goodsimg']=$matches['this'];
			if(empty($result['goodsimg']) && !empty($preg['preg_goodsimg2'])){
				preg_match($preg['preg_goodsimg2'],$html,$matches);
				$result['goodsimg']=$matches['this'];
				if(empty($result['goodsimg']) && !empty($preg['preg_goodsimg3'])){
					preg_match($preg['preg_goodsimg3'],$html,$matches);
					$result['goodsimg']=$matches['this'];
				}
			}
		}
		//抓取卖家
		$matches=array();
		if(empty($preg['preg_goodsseller'])){
			$result['goodsseller']=$preg['preg_goodsseller2'];
		}elseif(!empty($preg['preg_goodsseller'])){
			preg_match($preg['preg_goodsseller'],$html,$matches);
			$result['goodsseller']=$matches['this'];
			if(empty($result['goodsseller']) && !empty($preg['preg_goodsseller2'])){
				preg_match($preg['preg_goodsseller2'],$html,$matches);
				$result['goodsseller']=$matches['this'];
				if(empty($result['goodsseller']) && !empty($preg['preg_goodsseller3'])){
					preg_match($preg['preg_goodsseller3'],$html,$matches);
					$result['goodsseller']=$matches['this'];		
				}
			}
		}
		//抓取卖家url地址
		$matches=array();
		if(empty($preg['preg_sellerurl'])){
			$result['sellerurl']=$preg['preg_sellerurl2'];
		}elseif(!empty($preg['preg_sellerurl'])){
			preg_match($preg['preg_sellerurl'],$html,$matches);
			$result['sellerurl']=$matches['this'];
			if(empty($result['sellerurl']) && !empty($preg['preg_sellerurl2'])){
				preg_match($preg['preg_sellerurl2'],$html,$matches);
				$result['sellerurl']=$matches['this'];
				if(empty($result['sellerurl']) && !empty($preg['preg_sellerurl3'])){
					preg_match($preg['preg_sellerurl3'],$html,$matches);
					$result['sellerurl']=$matches['this'];	
				}
			}
		}
		$result['preg_goodsprice']=GetNum($result['preg_goodsprice']);
		$result['sendprice']=GetNum($result['sendprice']);
		$result['url']=$url;
		$result['goodsurl']=$url;
		$result['shopname']=$preg['shopname'];
		$result['shopurl']=$preg['shopurl'];
		return $result;//返回抓取到的数据
	}
	//获取网址对应的区配规则
	function getpreg($url){
		$arraydata=$this->table_shopsite->getdata("","state=1");
	
		foreach($arraydata as $value){
			if(strexists($url, $value['shopcode'])){
				return $value;
			}
		}
		return false;//找不到返回false
	}

}
?>