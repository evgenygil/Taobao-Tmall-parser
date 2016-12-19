<?php

function isemail($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}
function isURL($url = NULL) {        
	if($url==NULL) return false;          
	$protocol = '(http://|https://)';
	$allowed = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)';
	$regex = "^". $protocol .'(' . $allowed . '{1,63}\.)+'.'[a-z]' . '{2,6}';
	if(eregi($regex, $url)==true) return true;
	else return false;
}
function is_url($str){
	return preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/", $str);
}
function is_mobile($str){
  return preg_match("/^1(3|5|8)\d{9}$/ ", $str);
 }
?>