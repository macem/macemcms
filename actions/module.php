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

  
   case 'saveorder':

       allowMethod ('POST', 'Method Not Allowed', null, true);

       $post = Db::valid();

       if (!isset ($_SESSION['loggedin'])) {
	 header ("Location: ".url (Db::escape ($_POST['redirect']))."?info=You have no permission!");
       }

       $query = array();
       $langs = Lang::get();
       $table = $GLOBALS['config']['table'];

       foreach ($post as $key => $item) {
	 $param = explode ('#', $key);
	 array_push ($query , 'update content set order1='.$param[1].', position="'.$param[0].'" where uniq="'.$item['value'].'"');  
       }

       foreach ($query as $item) {
         $result = Db::query ($item);
       }

       //if ($result) redirect ($result, $_POST['redirect'], "Zmiany zostały zapisane");
       if ($result) setStatus ("Zmiany zostały zapisane", 'info', 0);

       break;

   case 'select':

      $token = allowMethod ('GET', 'Method Not Allowed', null, true);
 
      return '<form class="form-edit ajax-replace ajax-selform.form-add-module" action="'.url (true).'module.add'._if($_GET['position'], '', '?position='.$_GET['position']).'" method="post">
      <fieldset>
      <legend>Wybierz moduł:</legend>
      <input type="hidden" name="redirect" value="'.url (true).'#tab-defines!"/>
      <input type="hidden" name="token" value="'.$token.'"/>				
      <ul>
      <li><a href="'.url (true).'module.formarticle'._if($_GET['position'], '', '?position='.$_GET['position']).'" class="ajax-replace ajax-selform.form-add-module">Artykuł</a></li>
      <li><a href="'.url (true).'module.formarticle'._if($_GET['position'], '', '?position='.$_GET['position']).'&data=googlemap" class="ajax-replace ajax-selform.form-add-module">Google Map</a></li>
      <li><a href="'.url (true).'module.formarticle'._if($_GET['position'], '', '?position='.$_GET['position']).'&data=tabs" class="ajax-replace ajax-selform.form-add-module">Tabs</a></li>
      <li><a href="'.url (true).'module.formarticle'._if($_GET['position'], '', '?position='.$_GET['position']).'&data=slider" class="ajax-replace ajax-selform.form-add-module">Slider</a></li>
      <li><a href="'.url (true).'module.formgallery'._if($_GET['position'], '', '?position='.$_GET['position']).'" class="ajax-replace ajax-selform.form-add-module">Galeria</a></li>
      <li><a href="'.url (true).'module.formemailer'._if($_GET['position'], '', '?position='.$_GET['position']).'" class="ajax-replace ajax-selform.form-add-module">Email form</a></li>
      </ul>
      <div class="submit"><a href="'.url (true).'#tab-defines!" class="cancel">anuluj</a></div>
      </fieldset>
      </form>';

     break;
    

   case 'formarticle':
    $token = allowMethod ('GET', 'Method Not Allowed', null, true);

    $langs = Lang::get();
    $property = '';
    $tabs = array();
    $index = 0;
    $table = $GLOBALS['config']['table'];
    $uniq = $_GET['item'];
    $globalParam = '';
    
    $action = url ($table).'/module.addarticle';

    if (isset($uniq)) {
        $action .= '?item='.$uniq;
    }

    // TODO configurable - list with db row
     if (isset($_GET['position'])) {
        $property .= Controls::hidden (array (name=>'position', value=>$_GET['position'] ));
     }
     
    $allFields = array_merge (getAttr ($GLOBALS['config']['modules']['default']['field']), getAttr ($GLOBALS['config']['plugins'][$_GET['data']]['field']));
    
    //var_dump ($allFields);
    
    foreach ($langs as $key => $item) {

        // TODO list of contents where to find
       if (isset ($uniq)) {
           $content = Db::search ($data, 'uniq', $uniq, 'lang', $item['id']);
       }
       if (!isset($content[0]['title'])) { // TODO as data
           $content = Db::search ($GLOBALS['sidebar'], 'uniq', $uniq, 'lang', $item['id']);
       }
  
       $content[0]['table_name'] = $table;
  
       // read from first article with default language
       if ($langs[0]['path'] == $item['path']) {
         $params = getParam ($content[0]['params']); // TODO as global
         $globalParam = $params;
       }
  
       //$content_data = $content[0]['content'];
  
       // add config
       /*if (isset($_GET['data']) && isset($GLOBALS['config']['plugins'][$_GET['data']])) {
          foreach ($GLOBALS['config']['plugins'][$_GET['data']] as $key => $value) {
    
             // only if data has value
             if ($value) {
	 $params[$key] = $value;
             } 
    
          }
       }*/
       //$property .= Controls::hidden (array (name=>'params_'.$item['path'], value=>setParam($params) ));
  
       $property .= Controls::tabPane ('tab_'.$item['path'], ($index==$GLOBALS['config']['langid']?true:false));
       
       $property .= '<a href="#article_param" class="control-switch" title="więcej opcji|mniej opcji">więcej opcji</a>';
       
       $property .= renderFields ($allFields, $item, $content[0]);
  

       /*if (isset($_GET['data'])) {
        $property .= Controls::hidden (array (name=>'content_'.$item['path'], value=>_def ($content_data,'') )); 
       } else {
        $property .= Controls::textarea (array(label=>'Treść:', name=>'content_'.$item['path'], id=>'content_'.$table.$item['path'], classes=>'editable', rows=>'23', value=>_def ($content_data,'') ));
       }*/
  
       $property .= Controls::tabPaneEnd();
       $index++;
  
       $tabs['tab_'.$item['path']] = strtoupper ($item['path']);
    }
    
    //var_dump ($globalParam);

    $html = Controls::tabs ($tabs, $GLOBALS['config']['langid'], 'Wersja językowa');
    
    $allParams = array_merge (getAttr ($GLOBALS['config']['modules']['default']['config']), getAttr ($GLOBALS['config']['plugins'][$_GET['data']]['config']));
    
    //var_dump ($allParams);
    
    $html .= '<div class="data-options hide" id="article_param">'.renderParams ($allParams, $globalParam).'</div>'.$property;

    return editor ($content, $action, 'Edycja', $html, 'form-edit ajax-replace ajax-sel#art-'.$uniq);                

    break; 


   case 'addarticle':

        allowMethod ('POST', 'Method Not Allowed', null, true);

        $post = Db::valid();

        if (!isset ($_SESSION['loggedin'])) {
          header ("Location: ".url (Db::escape ($_POST['redirect']))."?info=You have no permission!");
        }

        $query = array();
        $langs = Lang::get();
        $table = $GLOBALS['config']['table'];
        $uniq = $_GET['item'];
        
        $unique = uniqid (); // unique id for lang/post
        
        $index = '(select count(*) as counter from content as f1 where table_name="'.$table.'" && position="'._def($post['position']['value'],'').'")';
        
        $globalParam = '';
        
        // TODO position
        
        //print_r ($post);

        foreach ($langs as $key => $item) {

           // check if exists article
           if (isset($uniq)) {
	$content = Db::search ($data, 'uniq', $uniq, 'lang', $item['id']);
           }
           if (!isset($content[0]['title'])) { // TODO as data
	$content = Db::search ($GLOBALS['sidebar'], 'uniq', $uniq, 'lang', $item['id']);
           }

           // read from first article with default language
           if ($langs[0]['path'] == $item['path']) {
             $params = getParam ($content[0]['params']); // article params
             $allParams = array_merge (getAttr ($GLOBALS['config']['modules']['default']['config']), getAttr ($GLOBALS['config']['plugins'][$params['module']]['config']));
             setParams ($allParams, $post, $params);
           }           

           $contents = $post['content_'.$item['path']]['value'];

           if (isset($params['data'])) {
              $contents = Db::escape ($post['content_'.$item['path']]['value']);
           }
           
           //var_dump ($allParams);

           // TODO
           // new article
           if (!isset($content[0]['title']) && !isset($uniq)) {
               array_push ($query, 'insert into content(uniq, title, table_name, content, modified, date, position, status, lang, params, author, order1) values("'.$unique.'", "'.$post['title_'.$item['path']]['value'].'", "'.$table.'", "'.cleanHTML ($contents).'", NOW(), NOW(),"'._def($post['position']['value'],'').'", 0, '.$item['id'].',"'.setParam($params).'", '.$_SESSION['userid'].', '.$index.')');

           } // article with new lang
           else if (!isset($content[0]['title']) && isset($uniq)) {
               array_push ($query, 'insert into content(uniq, title, table_name, content, modified, date, position, status, lang, params, author, order1) values("'.$uniq.'", "'.$post['title_'.$item['path']]['value'].'", "'.$table.'", "'.cleanHTML ($contents).'", NOW(), NOW(),"'._def($post['position']['value'],'').'", 0, '.$item['id'].',"'.setParam($params).'", '.$_SESSION['userid'].', '.$index.')');

           } // update article
           else {
               array_push ($query, 'update content set title="'.$post['title_'.$item['path']]['value'].'", content="'.cleanHTML ($contents).'", modified=NOW(), author='.$_SESSION['userid'].', params="'.setParam($params).'" where lang='.$item['id'].' && table_name="'.$table.'" && uniq="'.$uniq.'"');   
           }

        }

        print_r ($query);

       // multiple query TODO
       foreach ($query as $item) {
        $result = Db::query ($item);
        print_r ($result);
        //if (!$result) { redirect ($result, $_POST['redirect'], "Błąd zapisu ! ".$item); }	
       }

       //if ($result) redirect ($result, $_POST['redirect'], "Zmiany zostały zapisane");

       break;


   case 'confirm':
      $token = allowMethod ('GET', 'Method Not Allowed', null, true);
  
      $table = $GLOBALS['config']['table'];
      $uniq = $_GET['item'];
      
      // check if exists article
      if (isset($uniq)) {
          $content = Db::search ($data, 'uniq', $uniq, 'lang', $GLOBALS['config']['langid']);
      }
      if (!isset($content[0]['title'])) { // TODO as data
          $content = Db::search ($GLOBALS['sidebar'], 'uniq', $uniq, 'lang', $GLOBALS['config']['langid']);
      }
      
      $params = getParam ($content[0]['params']);
      
      return '<form action="'.url (true).$_GET['action'].'" method="post" class="ajax-replace ajax-method-'.$params['module'].' ajax-sel#art-'.$uniq.'">
       <input type="hidden" name="item" value="'.$uniq.'"/>
       <input type="hidden" name="data" value="'.$_GET['data'].'"/>
       <input type="hidden" name="redirect" value="'.url($table).'"/>
       <h3>Are you sure you want to '.$_GET['text'].'?</h3>
       <div class="submit">
       <input type="submit" name="submit" value="OK"/>
       lub <a href="'.url(true).'" class="cancel">cancel</a>
       </div>
       </form>';

    break;


   case 'removearticle':
       allowMethod ('POST', 'Method Not Allowed', null, true);

       //$data = Db::valid();

       if (!isset ($_SESSION['loggedin'])) {
	   header ("Location: ".url (Db::escape ($_POST['redirect']))."?info=You have no permission!");
       }

       $query = array();
       $langs = Lang::get();
       $table = $GLOBALS['config']['table'];
       
       $result = Db::query ('delete from content where uniq="'.$_POST['item'].'"');
       
       if ($result) redirect ($result, $_POST['redirect'], "Element usunięty");    
    
    break;


   case 'formgallery':
       $token = allowMethod ('GET', 'Method Not Allowed', null, true);

       $langs = Lang::get();
       $property = '';
       $tabs = array();
       $index = 0;
       $table = $GLOBALS['config']['table'];

       $action = url ($table).'/module.addgallery';

       if (isset($_GET['item'])) $action .= '?item='.$_GET['item'];             

       foreach ($langs as $key => $item) {

       if (isset($_GET['item'])) {
           $content = Db::search ($data, 'uniq', $_GET['item'], 'lang', $item['id']);
       }

       if (!isset($content[0]['title'])) { // TODO as data
        $content = Db::search ($GLOBALS['sidebar'], 'uniq', $_GET['item'], 'lang', $item['id']);
       }
       
       $content[0]['table_name'] = $table;
       
       $params = getParam ($content[0]['params']); // TODO as global

       if (isset($_GET['position'])) {
        $property .= Controls::hidden (array (name=>'position', value=>$_GET['position'] ));
       }
       
       $property .= Controls::hidden (array (name=>'params', value=>$content[0]['params'] ));
       
       $property .= Controls::tabPane ('tab_'.$item['path'], ($index==$GLOBALS['config']['langid']?true:false));
       //$property .= '<em class="custom">Modyfikowany : <strong>'.$content[0]['date'].'</strong></em>';
       $property .= Controls::input (array (label=>'Tytuł:', name=>'title_'.$item['path'], id=>'title_'.$table.$item['path'], maxlength=>'200', classes=>'sidy', value=>_def ($content[0]['title'],'') ));	
       //$property .= Controls::textarea (array(label=>'Treść:', name=>'content_'.$item['path'], id=>'content_'.$table.$item['path'], classes=>'editable', rows=>'23'/*, value=>$content[0]['content']*/ ));
       $property .= Controls::checkbox (array (label=>'Pinned:', name=>'param_pinned', id=>'param_pinned', classes=>'sidy checkbox', value=>$params['param_pinned'] ));
       $property .= Controls::tabPaneEnd();
       $index++;

       $tabs['tab_'.$item['path']] = strtoupper ($item['path']);
      }

       $property .= Controls::hidden (array (name=>'module', value=>'gallery' ));
       $property .= Controls::input (array (label=>'Gallery path:', name=>'field_gallerypath', value=>$params['field_gallerypath'] ));
       $property .= Controls::select (array ('normal','small'), array (label=>'Gallery view:', name=>'field_galleryview', value=>$params['field_galleryview'] ));

       return editor ($content, $action, 'Edycja', Controls::tabs ($tabs, $GLOBALS['config']['langid'], 'Wersja językowa').$property, 'form-edit ajax-replace ajax-sel#art-'.$_GET['item']);                

       break; 


   case 'addgallery':

       allowMethod ('POST', 'Method Not Allowed', null, true);

       $data = Db::valid();

       if (!isset ($_SESSION['loggedin'])) {
	   header ("Location: ".url (Db::escape ($_POST['redirect']))."?info=You have no permission!");
       }

       $query = array();
       $langs = Lang::get();
       $table = $GLOBALS['config']['table'];
       
       $params = getParam ($_POST['params']);
       
       $params['module'] = 'gallery';
       $params['field_gallerypath'] = $data['field_gallerypath']['value'];
       $params['field_galleryview'] = $data['field_galleryview']['value'];
       $params['param_pinned'] = $data['param_pinned']['value'];
       
       $unique = uniqid (); // unique id for lang/post

       $index = '(select count(*) as counter from content as f1 where table_name="'.$table.'" && position="'._def($_POST['position'],'').'")';

       foreach ($langs as $key => $item) {
        if (!isset($_GET['item'])) {
            array_push ($query ,'insert into content(uniq, title, table_name, content, modified, date, position, status, lang, params, author, order1) values("'.$unique.'", "'.$data['title_'.$item['path']]['value'].'", "'.$table.'", "", NOW(),NOW(),"'._def($_POST['position'],'').'", 0, '.$item['id'].',"'.setParam($params).'", '.$_SESSION['userid'].', '.$index.')');
	            } else {
            array_push ($query ,'update content set title="'.$data['title_'.$item['path']]['value'].'", modified=NOW(), params="'.setParam($params).'", author='.$_SESSION['userid'].' where lang='.$item['id'].' && table_name="'.$table.'" && uniq="'.$_GET['item'].'"');   
        }        		
       }

       // multiple query TODO
       foreach ($query as $item) {
	       $result = Db::query ($item);
	       //print_r ($result);
	       //if (!$result) { redirect ($result, $_POST['redirect'], "Błąd zapisu ! ".$item); }	
       }

       if ($result) redirect ($result, $_POST['redirect'], "Zmiany zostały zapisane");            
       break;  

   case 'formemailer':
    $token = allowMethod ('GET', 'Method Not Allowed', null, true);

    $langs = Lang::get();
    $property = '';
    $tabs = array();
    $index = 0;
    $table = $GLOBALS['config']['table'];

    $action = url ($table).'/module.addemailer';

    if (isset($_GET['item'])) $action .= '?item='.$_GET['item'];             

	foreach ($langs as $key => $item) {

      if (isset($_GET['item'])) {
	  $content = Db::search ($data, 'uniq', $_GET['item'], 'lang', $item['id']);
      }

      if (!isset($content[0]['title'])) { // TODO as data
       $content = Db::search ($GLOBALS['sidebar'], 'uniq', $_GET['item'], 'lang', $item['id']);
      }
      
      $content[0]['table_name'] = $table;
      
      $params = getParam ($content[0]['params']); // TODO as global
	
      if (isset($_GET['position'])) {
       $property .= Controls::hidden (array (name=>'position', value=>$_GET['position'] ));
      }
      
      $property .= Controls::hidden (array (name=>'params', value=>$content[0]['params'] ));
      
      $property .= Controls::tabPane ('tab_'.$item['path'], ($index==$GLOBALS['config']['langid']?true:false));
      //$property .= '<em class="custom">Modyfikowany : <strong>'.$content[0]['date'].'</strong></em>';
      $property .= Controls::input (array (label=>'Tytuł:', name=>'title_'.$item['path'], id=>'title_'.$table.$item['path'], maxlength=>'200', classes=>'sidy', value=>_def ($content[0]['title'],'') ));	
      //$property .= Controls::textarea (array(label=>'Treść:', name=>'content_'.$item['path'], id=>'content_'.$table.$item['path'], classes=>'editable', rows=>'23'/*, value=>$content[0]['content']*/ ));
      $property .= Controls::checkbox (array (label=>'Pinned:', name=>'param_pinned', id=>'param_pinned', classes=>'sidy checkbox', value=>$params['param_pinned'] ));
      $property .= Controls::tabPaneEnd();
      $index++;
      
      $tabs['tab_'.$item['path']] = strtoupper ($item['path']);
   }
    
    $property .= Controls::hidden (array (name=>'module', value=>'contact' ));
    //$property .= Controls::input (array (label=>'Gallery path:', name=>'field-gallerypath', id=>'field-gallerypath' ));

    return editor ($content, $action, 'Edycja', Controls::tabs ($tabs, $GLOBALS['config']['langid'], 'Wersja językowa').$property, 'form-edit ajax-replace ajax-sel#art-'.$_GET['item']);                
    
    break; 
       
   case 'addemailer':

    allowMethod ('POST', 'Method Not Allowed', null, true);
    
    $data = Db::valid();

    if (!isset ($_SESSION['loggedin'])) {
	header ("Location: ".url (Db::escape ($_POST['redirect']))."?info=You have no permission!");
    }
    
    $query = array();
    $langs = Lang::get();
    $table = $GLOBALS['config']['table'];
    

   $params = getParam ($_POST['params']);

   $params['module'] = 'contact';
   $params['param_pinned'] = $data['param_pinned']['value'];
    
    $unique = uniqid (); // unique id for lang/post
    
    $index = '(select count(*) as counter from content as f1 where table_name="'.$table.'" && position="'._def($_POST['position'],'').'")';

    foreach ($langs as $key => $item) {
     if (!isset($_GET['item'])) {
         array_push ($query ,'insert into content(uniq, title, table_name, content, modified, date, position, status, lang, params, author, order) values("'.$unique.'", "'.$data['title_'.$item['path']]['value'].'", "'.$table.'", "", NOW(),NOW(),"'._def($_POST['position'],'').'", 0, '.$item['id'].',"'.setParam($params).'", '.$_SESSION['userid'].', '.$index.')');
	         } else {
         array_push ($query ,'update content set title="'.$data['title_'.$item['path']]['value'].'", modified=NOW(), author='.$_SESSION['userid'].', params="'.setParam($params).'" where lang='.$item['id'].' && table_name="'.$table.'" && uniq="'.$_GET['item'].'"');   
     } 
    }

    //print_r ($query); 
    
    // multiple query TODO
    foreach ($query as $item) {
	    $result = Db::query ($item);
	    //print_r ($result);
	    //if (!$result) { redirect ($result, $_POST['redirect'], "Błąd zapisu ! ".$item); }	
    }

    if ($result) redirect ($result, $_POST['redirect'], "Zmiany zostały zapisane");            
    break;                                                    	
  }
 }

};
?>