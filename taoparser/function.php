<?php

function getDomain($url) {

  $pieces = parse_url($url);
  $domain = isset($pieces['host']) ? $pieces['host'] : '';
  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
    return $regs['domain'];
  }
  return false;
}

function sipp($input) {

    if(get_magic_quotes_gpc ())
    {
        $input = stripslashes ($input);
        // убрали лишнее - теперь экранирование.
    }
  
    $input = strip_tags($input);
    //----------- режем теги.
	$input = preg_replace('/(<(link|script|iframe|object|applet|embed).*?>[^<]*(<\/(link|script|iframe|object|applet|embed).*?>)?)/i', '', $input); //Удаляем стили, скрипты, фреймы и flash
    $input = preg_replace('/(script:)|(expression\()/i', '\\1&nbsp;', $input); //Обезвреживаем скрипты, что остались
    $input = preg_replace('/(onblur|onchange|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onload|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onreset|onselect|onsubmit|onunload)=?/i', '', $input); //Удаляем события
    $input = preg_replace('/((src|href).*?=.*?)(http:\/\/)/i', '\\1redirect/\\2', $input); //Обезвреживаем внешние ссылк
	  return $input;

}

?>