<?php

/**
 * class Module Util
 * @author macem
 * @package macemCMS
*/

class Module extends Db
{

 /**
  * method connect to database
  * @param $db - database: [mysql]
 */
 function action ($name, $data) {
  switch ($name) {

   case 'formdata':
      $token = allowMethod ('GET', 'Method Not Allowed', null, true);
  
      $langs = Lang::get();
      $property = '';
      $tabs = array();
      $index = 0;
      $table = $GLOBALS['config']['table'];
  
      $action = url ($table).'/data.adddata';
      if (isset($_GET['item'])) {
         $action .= '?item='.$_GET['item'];
      }
      
      if (isset($_GET['position'])) {
         $property .= Controls::hidden (array (name=>'position', value=>$_GET['position'] ));
      }
  
      foreach ($langs as $key => $item) {
  
         if (isset($_GET['item'])) {
            $content = Db::search ($data, 'uniq', $_GET['item'], 'lang', $item['id']);
         }
         if (!isset($content[0]['title'])) { // TODO as data
            $content = Db::search ($GLOBALS['sidebar'], 'uniq', $_GET['item'], 'lang', $item['id']);
         }
    
         $content[0]['table_name'] = $table;
    
         $params = getParam ($content[0]['params']); // TODO as global
    
         $pages = json_decode (utf8_decode ($content[0]['content']), true);

         // store content
         $property .= Controls::hidden (array (name=>'content_'.$item['path'], value=>$content[0]['content'] ));
    
         $property .= Controls::tabPane ('tab_'.$item['path'], ($index==$GLOBALS['config']['langid']?true:false));
         
         $field_params = getAttr ($params['data']);
    
         // ARTICLE SHOULD BE ADDED - then data can be added
    
         if (isset($_GET['data'])) { // existing data
    
            $property .= renderData ($field_params, $_GET['data'], $item, $pages[$_GET['data']]);
    
         } else { // new data
    
            foreach ($field_params as $field_key => $field) {
	$name = 'field_'.$item['path'].'_'.$_GET['data'].'_'.$field_key.'_new';
        
	$field_name = array_shift (explode (':', $field_key));
        
	/*$field = array(
	    'label'    => $field[1],
	    'classes'  => $field[2],
	    'size'     => $field[3],
	    'disabled' => $field[4],
	);*/
	$fields = array (0 => $field);
	$field = Field::render ($fields, 0, '');
        
	$property .= $GLOBALS['config']['field'][$field_name]( $name, $item, $field);
            }
      
            $content[0]['new'] = 'true';
         }
         
         if ($params['module']) {
            $module = $params['module'];
         }

         $property .= Controls::hidden (array (name=>'dataid', value=>$content[0]['params'] ));
    
         $property .= Controls::tabPaneEnd();
         $index++;

         $tabs['tab_'.$item['path']] = strtoupper ($item['path']);
      }
  
      return editor ($content, $action, 'Edycja', Controls::tabs ($tabs, $GLOBALS['config']['langid'], 'Wersja językowa').$property, 'form-edit ajax-method-'.$module.' ajax-replace ajax-sel#art-'.$_GET['item']);                

    break;


   case 'adddata-json':

      allowMethod ('POST', 'Method Not Allowed', null, true);
  
      $post = Db::valid();
  
      if (!isset ($_SESSION['loggedin'])) {
         header ("Location: ".url (Db::escape ($_POST['redirect']))."?info=You have no permission!");
      }
  
      $query = array();
      $langs = Lang::get();
      $table = $GLOBALS['config']['table'];
      $uniq = $_GET['item'];
      $dataId = $_GET['data'];
      
      // TODO we can add global value for all lang or we can add only for one lang
      
      foreach ($langs as $key => $item) {
  
         if (isset($uniq)) {
            $content = Db::search ($data, 'uniq', $uniq, 'lang', $item['id']);
         }
         if (!isset($content[0]['title'])) { // TODO as data
            $content = Db::search ($GLOBALS['sidebar'], 'uniq', $uniq, 'lang', $item['id']);
         }
    
         $content[0]['table_name'] = $table;
    
         $params = getParam ($content[0]['params']); // TODO as global
    
         $pages = json_decode (utf8_decode($content[0]['content']), true);
    
         //print_r ($pages);
    
         foreach ($post as $field_key => $field) {
            //echo $field_key.'-'.stripcslashes ($field['value']);
            $pages[$dataId][$field_key]['value'] = stripcslashes ($field['value']);
         }
      }
      
      //print_r ($pages);
  
      foreach ($langs as $key => $item) {
          array_push ($query, 'update content set content="'.Db::escape (str_replace ('\u', '\\\u', str_replace ('\"', '\\\"', utf8_encode (json_encode($pages))))).'", modified=NOW(), author='.$_SESSION['userid'].' where lang='.$item['id'].' && table_name="'.$table.'" && uniq="'.$uniq.'"');   
      }
  
      // multiple query TODO
      foreach ($query as $item) {
         $result = Db::query ($item);
         if (!$result) { echo "Błąd zapisu ! ".$items; }	
      }    

    break;


   case 'adddata':

      allowMethod ('POST', 'Method Not Allowed', null, true);

      $post = Db::valid();

      if (!isset ($_SESSION['loggedin'])) {
           header ('Location: '.url (Db::escape ($_POST['redirect'])).'?info=You have no permission!');
      }

      $query = array();
      $langs = Lang::get();
      $table = $GLOBALS['config']['table'];
      $uniq = $_GET['item'];

      foreach ($langs as $key => $item) {
         
         $pages = json_decode (utf8_decode($_POST['content_'.$item['path']]), true); // must be a $_POST
   
         if (is_array ($pages)) {
            $keys = array_keys ($pages);
            $indexNew = $keys[count($keys)-1] + 1;
         } else {
            $indexNew = 0; 
         }
         
         //var_dump($pages);
   
         foreach ($post as $field_key => $field) {
            if (strpos ($field_key, 'field_'.$item['path']) !== false) {
	$indexes = explode ('_', $field_key);
             
	if ($indexes[4] == 'new') {
	   $pages[$indexNew][$indexes[3]]['value'] = stripcslashes (cleanHTML ($field['value']));
	   $pages[$indexNew][$indexes[3]]['order'] = count ($pages[$indexNew])-1;
	} else {
	   $pages[$indexes[2]][$indexes[3]]['value'] = stripcslashes (cleanHTML ($field['value']));
	}
            }
         }

         if (!isset($_GET['item'])) {
            //array_push ($query, 'insert into content(uniq, title, table_name, content, modified, date, position, status, lang, author, order1) values("'.$unique.'", "'.$data['title_'.$item['path']]['value'].'", "'.$table.'", "'.cleanHTML ($data['content_'.$item['path']]['value']).'", NOW(), NOW(),"'._def($_POST['position'],'').'", 0, '.$item['id'].', '.$_SESSION['userid'].', '.$index.')');
         } else {
            array_push ($query, 'update content set content="'.Db::escape (str_replace ('\u', '\\\u', str_replace ('\"', '\\\"', utf8_encode (json_encode($pages))))).'", modified=NOW(), author='.$_SESSION['userid'].' where lang='.$item['id'].' && table_name="'.$table.'" && uniq="'.$uniq.'"');   
         }
      }

     // multiple query TODO
     foreach ($query as $item) {
        //echo $item;
        $result = Db::query ($item);
        //print_r ($result);
        //if (!$result) { redirect ($result, $_POST['redirect'], "Błąd zapisu ! ".$item); }
        if (!$result) { echo "Błąd zapisu ! ".$items; }	
     }

     if ($result) { redirect ($result, $_POST['redirect'], "Zmiany zostały zapisane"); }

     break;


   case 'removedata':
     allowMethod ('POST', 'Method Not Allowed', null, true);

     //$data = Db::valid();

     if (!isset ($_SESSION['loggedin'])) {
	 header ("Location: ".url (Db::escape ($_POST['redirect']))."?info=You have no permission!");
     }

     $query = array();
     $langs = Lang::get();
     $table = $GLOBALS['config']['table'];
     $dataId = $_POST['data'];
     $uniq = $_POST['item'];
     
     foreach ($langs as $key => $item) {
         
         if (isset($uniq)) {
            $content = Db::search ($data, 'uniq', $uniq, 'lang', $item['id']);
         }
    
         if (!isset($content[0]['title'])) { // TODO as data
            $content = Db::search ($GLOBALS['sidebar'], 'uniq', $uniq, 'lang', $item['id']);
         }
    
         $content[0]['table_name'] = $table;
    
         $params = getParam ($content[0]['params']); // TODO as global
    
         $pages = json_decode (utf8_decode ($content[0]['content']), true);
         
         unset ($pages[$dataId]);
         
         //var_dump ($pages);
         
         array_push ($query, 'update content set content="'.Db::escape(str_replace('\u', '\\\u', json_encode($pages))).'", modified=NOW(), author='.$_SESSION['userid'].' where lang='.$item['id'].' && table_name="'.$table.'" && uniq="'.$uniq.'"');
         
     }

     // multiple query TODO
     foreach ($query as $item) {
        //echo $item;
        $result = Db::query ($item);
        //print_r ($result);
        //if (!$result) { redirect ($result, $_POST['redirect'], "Błąd zapisu ! ".$item); }
        if (!$result) { echo "Błąd zapisu ! ".$items; }	
     }

     if ($result) redirect ($result, $_POST['redirect'], "Element usunięty");
    
    break;
   
   
   case 'addconfig-json':

      allowMethod ('POST', 'Method Not Allowed', null, true);
  
      $post = Db::valid();
  
      if (!isset ($_SESSION['loggedin'])) {
         header ("Location: ".url (Db::escape ($_POST['redirect']))."?info=You have no permission!");
      }
  
      $query = array();
      $langs = Lang::get();
      $table = $GLOBALS['config']['table'];
      $uniq = $_GET['item'];
      $dataId = $_GET['data'];
      
      /*foreach ($langs as $key => $item) {
  
       if (isset($uniq)) {
        $content = Db::search ($data, 'uniq', $uniq, 'lang', $item['id']);
       }
  
       if (!isset($content[0]['title'])) { // TODO as data
        $content = Db::search ($GLOBALS['sidebar'], 'uniq', $uniq, 'lang', $item['id']);
       }
  
       $content[0]['table_name'] = $table;
  
       $params = getParam ($content[0]['params']); // TODO as global
  
       $pages = json_decode (utf8_decode($content[0]['content']), true);
  
       //print_r ($pages);
  
       foreach ($post as $field_key => $field) {
        //echo $field_key.'-'.stripcslashes ($field['value']);
        $pages[$dataId][$field_key]['value'] = stripcslashes ($field['value']);
       }
      }*/
      
      //print_r ($pages);
  
      foreach ($langs as $key => $item) {
          //array_push ($query, 'update content set content="'.Db::escape(str_replace('\u', '\\\u', json_encode($pages))).'", modified=NOW(), author='.$_SESSION['userid'].' where lang='.$item['id'].' && table_name="'.$table.'" && uniq="'.$uniq.'"');   
      }
  
      // multiple query TODO
      foreach ($query as $item) {
         //$result = Db::query ($item);
         //if (!$result) { echo "Błąd zapisu ! ".$items; }	
      }    

    break;

  }
 }

};
?>