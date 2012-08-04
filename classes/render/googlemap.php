<?php

/**
 * class Module Gallery
 * @author macem
 * @package macemCMS
*/

    require_once (PHP_PATH.'classes/images.php');

    $item['edit_url'] = url (true).'module.formarticle?item='.$article['uniq'].'&data=googlemap';
    $item['remove_url'] = url (true).'module.confirm?item='.$article['uniq'].'&text=this map&action=module.removearticle';
    
    //array_unshift ($GLOBALS['config']['js'], 'http://maps.googleapis.com/maps/api/js?key=AIzaSyASQ3uQhokKntozL2pHB5km_IGTrHr0V-c&sensor=false');
    //array_unshift ($GLOBALS['config']['js'], JS.'js/plugins/jquery.google-map.js');
    $GLOBALS['config']['js']['googlemap'] = 'http://maps.googleapis.com/maps/api/js?key=AIzaSyASQ3uQhokKntozL2pHB5km_IGTrHr0V-c&sensor=false';
    $GLOBALS['config']['js']['googlemap-load'] = JS.'js/plugins/jquery.google-map.js';
    
    //array_unshift ($GLOBALS['config']['css'], JS.'js/plugins/css/jquery.textslide.css');
    
    $pages = json_decode (str_replace ('\\u', '\u', $item['content']), true); // base64_decode fix bug in serialize

    $item['params']['config'] = getAttr ($item['params']['config']);
    
    //print_r ($item['params']['config']);
    
    $item['add_data_json_url'] = url (true).'data.adddata-json?item='.$article['uniq'].'&data=';
    
    $item['add_data_url'] = url (true).'data.formdata?item='.$article['uniq'];
    $item['edit_data_url'] = url (true).'data.formdata?item='.$article['uniq'].'&data=';
    $item['remove_data_url'] = url (true).'module.confirm?item='.$article['uniq'].'&text=this item&action=data.removedata&data=';
    
    $item['content'] = $pages;

?>