<?php

/**
 * class Module Gallery
 * @author macem
 * @package macemCMS
*/

require_once (PHP_PATH.'classes/images.php');

$item['edit_url'] = url (true).'module.formarticle?item='.$article['uniq'].'&data=gallery';
$item['remove_url'] = url (true).'module.confirm?item='.$article['uniq'].'&text=this gallery&action=module.removegallery';

$item['add_data_url'] = url (true).'photo.form?item=new&path='.urlencode($path);

$path = $item['params']['gallerypath'];
$view = $item['params']['galleryview'];

$images = Images::thumbnails ($path);

$item['view'] = $view;

//if (is_string ($images)) { $item['content'] = $images; }
$data = array();

while ($image = array_shift ($images)) {
    $items = array();
    
    $items['edit_data_url'] = 'photo.form?item=delete&file='.urlencode ($image['href']);
    $items['size'] = $image['filesize'];
    $items['mime'] = $image['info']['mime'];
    $items['href'] = HOST.str_replace ('-thumbnail', '', $image['href']);
    $items['src'] = HOST.$image['href'];
    $items['downloadurl'] = $image['info']['mime'].":".str_replace ('-thumbnail', '', $image['name']).":".HOST.str_replace ('-thumbnail', '', $image['href']);
    
    array_push ($data, $items);
}

$item['content'] = $data;
?>