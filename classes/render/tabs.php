<?php

/**
 * class Module Gallery
 * @author macem
 * @package macemCMS
*/


require_once (PHP_PATH.'classes/images.php');

    $item['edit_url'] = url (true).'module.formarticle?item='.$article['uniq'].'&data=tabs';
    $item['remove_url'] = url (true).'module.confirm?item='.$article['uniq'].'&text=all tabs&action=module.removearticle';
    
    //array_unshift ($GLOBALS['config']['js'], JS.'js/plugins/jquery.textslide.auto.js');
    //array_unshift ($GLOBALS['config']['js'], JS.'js/plugins/jquery.textslide.breadcrumb.js');
    //array_unshift ($GLOBALS['config']['js'], JS.'js/plugins/jquery.textslide.js');
    
    $GLOBALS['config']['css']['tabs'] = EDIT_CDN.'tabs.css';
    
    $pages = json_decode (str_replace ('\\u', '\u', $item['content']), true); // base64_decode fix bug in serialize
    
    $item['add_data_url'] = url (true).'data.formdata?item='.$article['uniq'];
    $item['edit_data_url'] = url (true).'data.formdata?item='.$article['uniq'].'&data=';
    $item['remove_data_url'] = url (true).'module.confirm?item='.$article['uniq'].'&text=this tab&action=data.removedata&data=';
    
    $item['content'] = $pages;

?>