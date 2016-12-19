<?php
include("common.inc.php");
include("function.php");
InitGP(array("action","url","refuname","referer","aid","cityid")); //初始化变量全局返回
include(INC_PATH."/shopsite.class.php");
$shopsite=ShopClass::init();
AjaxHead();

if ($_GET['link'] != '') $link = $_GET['link']; else $link = sipp($_POST[link]);
$link=trim($link);
if( substr($link,0,7)!="http://" and substr($link,0,8)!="https://") $link="http://".$link;

$host = getDomain($link);

if  ($host == "ebay.com")
		{
		$detail_url = $link;
		$title = "Ebay Item";
		}
else if  ($host == "yahoo.com")
		{
		$detail_url = $link;
		$title = "Yahoo Item";
		}
else if  ($host == "amazon.com")
		{
		$detail_url = $link;
		$title = "Amazon Item";
		}
else if ($host == "taobao.com" OR $host == "tmall.com" OR $host == "1688.com")
	 {
		//$url=str_replace("tmall","taobao",$link);
		$link2=$link;
		
		$Array1=parse_url($link);
		parse_str($Array1[query], $result);
		if($Array1['host'] == "item.taobao.com"){
			$link2="http://world.taobao.com/item/".$result[id].".htm"; 
			/*if ($host == "tmall.com")
				$link2="http://taiwan.tmall.com/item/".$result[id].".htm";*/
			}
		if($Array1['host'] == "tw.taobao.com")
			$link2=str_replace("tw.taobao.com","world.taobao.com",$link);
		
			
			
				/*		if ($Array1['host'] == "tw.taobao.com")
			 {
			 preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63})\.[a-z\.]{2,6}$/i', $Array1['path'], $regs);
    		$link2="http://item.taobao.com/item.htm?id=".$regs['domain']; 
			 }*/
			//echo $link2;
	  	$info=$shopsite->get($link2);
	 
		if(empty($info))$data['_statusCode']=500;
	//echo $link2;
		$result_item['d']=array('Href'=>$info['url'],'Name'=>$info['goodsname'],'BuyNum'=>$info['goodsnum'],'Freight'=>$info['sendprice'],'IsAuction'=>0,'IsFreightFree'=>'false','Picture'=>$info['goodsimg'],'Price'=>$info['goodsprice'],'Remark'=>'','Shop'=>array('Href'=>$info['sellerurl'],'Name'=>$info['goodsseller'],'Credit'=>0,'DeliverySpeed'=>0,'PositiveRatio'=>0,'ServiceAttitude'=>0,'Trueness'=>0),'Thumbnail'=>$info['goodsimg'],'VIPPrice1'=>-1,'VIPPrice2'=>-1,'VIPPrice3'=>-1,'Error'=>'','UserGroup'=>0);
		foreach($result_item['data'] as $result) {
  		  echo $result['type'], '<br>';
		}		
	$title = $result_item['d']['Name'];
	//$express_fee = $result_item['d']['Freight'];
	$express_fee = "manual";
	$detail_url = $link;
	$price = $result_item['d']['Price'];
	$matches = null;
	$returnValue = preg_match('/\\d*.\\d*/', $price, $matches);
	$price=$matches[0];
	$pic_url = $result_item['d']['Picture'];

	}
else
	{
         $detail_url = $link;
//	 echo("<META HTTP-EQUIV=refresh CONTENT='0;url=/index.php?err=1'>");
	}
	
include_once("../config.php");
include ("../header.php");

if(!$_SESSION["loggedIn"])
			{
				echo "<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=http://guron.biz/login.php\">";
				exit();
			}
			
if ($_POST[option] == 'addtoorder') 
		 {
		  $tlink = '/add_to_order.php';
		  $zidz = $_POST[zid];
		  $button = '<input type="submit" value="Добавить в заказ" style="margin-bottom: -7px; margin-left: 260px"';
		 }
		else if ($_POST[option] == 'wishlist') 
		 {
		 $tlink = '/wishlist.php';
		 $button = '<input type="submit" style="margin-bottom: -7px; margin-left: 260px" value="Добавить в Wishlist" >';
		 }
		 else
		  {
		 $tlink = '/add_product.php';
		 $button = '<input name="submit" type="image" src="http://guron.biz/img/buttons/button_03.gif" style="margin-bottom: -7px; margin-left: 260px" value="Добавить в корзину" onmouseover="this.src=\'http://guron.biz/img/buttons/button-up_03.gif\'" onmouseout="this.src=\'http://guron.biz/img/buttons/button_03.gif\'">';
		 }
		 

?>
		<br /><div>
		<form name="form" action="<? echo $tlink ?>" method="post">
		<table cellspacing="2" cellpadding="3" border="0" style="font-size: 12px; border:0 solid;" align="left" class="ptable">
		<tr valign="top">
			<th rowspan="7"><img src="<?php echo $pic_url; ?>" height="180px"/></th>
			<td><b>Ссылка на товар: </b><a href="<?php echo $detail_url; ?>"><?php  echo substr($detail_url, 0, 40)."..."; ?></a></td>
		</tr>
		<tr>
			<td><b>Название товара: </b><b> <?php echo $title ?></b></td>
		</tr>
<?php if (!$price): ?>	
		<tr>
			<td><b><font color="red">Не удалось получить цену. Пожалуйста, проставьте цену самостоятельно!</font></b></td>
		</tr>
<?php endif; ?>	
		<tr>
			<td><b>Цена: </b><font color="red">*</font> </b><input name="vprice" type="number" size="6"  min="0" step="any" value="<?php echo $price ?>" required> CNY</td>
			<?php if ($_GET[errp]==1) echo "<td><font color=\"red\">Введите цену за единицу товара!</font></td>"; ?></tr>
		<tr>
		<td><font color="red">
		<b>(Заполняется самостоятельно только в том случае, если на товар действует скидка, либо если сайт "не поймал" цену вашего товара.<br />
		Цена со скидкой действует только при наличии комментария к позиции "buy only at discount", в противном случае товар будет выкуплен за полную цену. <br />
		По VIP-ценам товары мы не выкупаем)</b></font></td>
		</tr>
		<tr>
			<td><b>Доставка по Китаю: </b><?php if($express_fee == false) echo "Неизвестна";else if ($express_fee == "manual") echo "заполняется китайцами"; else echo $express_fee; ?> CNY&nbsp;&nbsp;&nbsp;<i><font color="gray">( <?php echo tobucks($express_fee) ?> $ )</font></i></td>
		</tr>
		<tr>
			<td><b>Количество: </b><input name="vpack" type="number" size="3" value="1" width="2" min="1" max="200"></td>
		</tr>

		<tr>
			<td>
		<b>Комментарий к заказу (цвет, размер...): </b><br><br><textarea name="vcomment" cols="54" rows="3" style="font-size:12px; font-family: Tahoma;" autofocus></textarea><br><br>
		<iframe width="400" height="258" src="http://www.youtube.com/embed/CMQvE23fvq8" frameborder="0" allowfullscreen></iframe><br><br>

		<div style="width: 400px;">&nbsp;&nbsp;<font color="red"><b>Если ваши комментарии будут составлены в иной форме – ответственность за цвет , размер ваших вещей мы не несем и претензии не принимаем. Так, русский язык, переведенный на английский через переводчик, не всегда может корректно передать детали.</font></b><br><br></div>

		<? echo $button; ?>
		</br></td>

		</tr>
		 <td>
		<input name="zidz" type="hidden" value="<?php echo $zidz ?>">
		<input name="vname" type="hidden" value="<?php echo $title ?>">
		<input name="vimg" type="hidden" value="<?php echo $pic_url; ?>" >
		<input name="vlink" type="hidden" value="<?php echo $detail_url ?>" >
		<input name="vtao" type="hidden" value="<?php echo $num_iid ?>" >
		<input name="vdelivery_price" type="hidden" value="<?php //echo $express_fee; ?>"><br>
		</form>
		</td>
		<tr>
		</tr>
		</table>
<!-- <?php if($has_discount=true) echo "Для этого товара имеется скидка.</br>Конечная цена товара может отличаться.</br>" ?> -->