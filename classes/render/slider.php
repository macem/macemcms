<?php

/**
 * class Module Gallery
 * @author macem
 * @package macemCMS
*/


require_once (PHP_PATH.'classes/images.php');

$item['edit_url'] = url (true).'module.formarticle?item='.$article['uniq'].'&data=slider';
$item['remove_url'] = url (true).'module.confirm?item='.$article['uniq'].'&text=this slider&action=module.removearticle';

$GLOBALS['config']['js']['slider'] = JS.'js/plugins/jquery.textslide.js';
$GLOBALS['config']['js']['slider-auto'] = JS.'js/plugins/jquery.textslide.auto.js';
$GLOBALS['config']['js']['slider-breadcrumb'] = JS.'js/plugins/jquery.textslide.breadcrumb.js';

$GLOBALS['config']['css']['slider'] = JS.'js/plugins/css/jquery.textslide.css';

$pages = json_decode (str_replace ('\\u', '\u', $item['content']), true); // base64_decode fix bug in serialize

$item['add_data_url'] = url (true).'data.formdata?item='.$article['uniq'];
$item['edit_data_url'] = url (true).'data.formdata?item='.$article['uniq'].'&data=';
$item['remove_data_url'] = url (true).'module.confirm?item='.$article['uniq'].'&text=this slide&action=data.removedata&data=';

$item['content'] = $pages;

?>