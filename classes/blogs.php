<?php

/**
 * class Pages
 * @author macem
 * @package macemCMS
*/

class Blogs extends Db 
{
    /**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	
	function remove ($id) {
		//allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);
		
		//$page = Pages::getPage ($id);
		
		//$result = Db::query ('delete from pages where id='.$id);
		
		$result = Db::query ('delete from content where uniq="'.$id.'"');	
        
		//redirect ($result, $_POST['redirect'], 'Post "'.$id.'" został usunięty.');		
	}

    
    function form ($id) {
        $token = allowMethod ('GET', 'Method Not Allowed', null, true);

		$langs = Lang::get();
		$property = '';
		$tabs = array();
		$index = 0;
		$table = $GLOBALS['config']['table'];
			
        if ($id == 'new') { // TODO
			
			foreach ($langs as $key => $item) {
				//$content = Db::search ($GLOBALS['content'], 'table_name', $table, 'lang', $item['id']);
				$content = array ('0' => array (
					'table_name' => $table
				));
				
				$property .= Controls::tabPane ('tab_'.$item['path'], ($index==$GLOBALS['config']['langid']?true:false));
				//$property .= '<em class="custom">Modyfikowany : <strong>'.$content[0]['date'].'</strong></em>';
				$property .= Controls::input (array (label=>'Tytuł:', name=>'title_'.$item['path'], id=>'title_'.$table.$item['path'], maxlength=>'200', classes=>'sidy'/*, value=>$content[0]['title']*/ ));	
				$property .= Controls::textarea (array(label=>'Treść:', name=>'content_'.$item['path'], id=>'content_'.$table.$item['path'], classes=>'editable', rows=>'23'/*, value=>$content[0]['content']*/ ));
				$property .= Controls::tabPaneEnd();
				$index++;
				
				$tabs['tab_'.$item['path']] = strtoupper ($item['path']);
			}
					
	        return editor ($content, url ($table).'/blog.save?item='.$id, 'Edycja', Controls::tabs ($tabs, $GLOBALS['config']['langid'], 'Wersja językowa').$property);

        } else { // edit
			
			foreach ($langs as $key => $item) {
				$content = Db::search ($GLOBALS['content'], 'uniq', $id, 'lang', $item['id']);
				
				$property .= Controls::tabPane ('tab_'.$item['path'], ($index==$GLOBALS['config']['langid']?true:false));
				$property .= '<em class="custom">Modyfikowany : <strong>'.$content[0]['date'].'</strong></em>';
				$property .= Controls::input (array (label=>'Tytuł:', name=>'title_'.$item['path'], id=>'title_'.$table.$item['path'], maxlength=>'200', classes=>'sidy', value=>$content[0]['title'] ));	
				$property .= Controls::textarea (array(label=>'Treść:', name=>'content_'.$item['path'], id=>'content_'.$table.$item['path'], classes=>'editable', rows=>'23', value=>$content[0]['content'] ));
				$property .= Controls::tabPaneEnd();
				$index++;
				
				$tabs['tab_'.$item['path']] = strtoupper ($item['path']);
			}
					
	        return editor ($content, url ($table).'/blog.save?item='.$id, 'Edycja', Controls::tabs ($tabs, $GLOBALS['config']['langid'], 'Wersja językowa').$property);

        }
    }
    
    function add ($data) {

        allowMethod ('POST', 'Method Not Allowed', null, true);

        if (!isset ($_SESSION['loggedin'])) {
            header ("Location: ".url (Db::escape ($_POST['redirect']))."?info=You have no permission!");
        }
		
		$query = array();
		$langs = Lang::get();
		$table = $GLOBALS['config']['table'];
		
		echo "<!--".$table."-->";
		
		$unique = uniqid (); // unique id for lang/post
		
		foreach ($langs as $key => $item) {
			array_push ($query ,'insert into content(uniq, title, table_name, content, date, position, status, lang, params) values("'.$unique.'", "'.$data['title_'.$item['path']]['value'].'", "'.$table.'", "'.cleanHTML ($data['content_'.$item['path']]['value']).'", NOW(),"", 0, '.$item['id'].',"")');
		}
		
		// multiple query TODO
		foreach ($query as $item) {
			$result = Db::query ($item);
			//print_r ($result);
			//if (!$result) { redirect ($result, $_POST['redirect'], "Błąd zapisu ! ".$item); }	
		}
		
		if ($result) redirect ($result, $_POST['redirect'], "Zmiany zostały zapisane");
    }

    function update ($data, $id) {

        allowMethod ('POST', 'Method Not Allowed', null, true);

        if (!isset ($_SESSION['loggedin'])) {
            header ("Location: ".url(Db::escape ($_POST['redirect']))."?info=You have no permission!");
        }
		
		$query = array();
		$langs = Lang::get();
		$table = $GLOBALS['config']['table'];
		
		$unique = uniqid (); // unique id for lang/post
		
		foreach ($langs as $key => $item) {
			//$content = Db::search ($GLOBALS['content'], 'table_name', $table, 'lang', $item['id']);

			//if ($content[0]['content']) {
				array_push ($query ,'update content set title="'.$data['title_'.$item['path']]['value'].'", content="'.cleanHTML ($data['content_'.$item['path']]['value']).'", modified=NOW() where lang='.$item['id'].' && table_name="'.$table.'" && uniq="'.$id.'"');
			//} else {
			//	array_push ($query ,'insert into content(uniq, title, table_name, content, date, status, lang) values("'.$unique.'", "'.$data['title_'.$item['path']]['value'].'", "'.$table.'", "'.cleanHTML ($data['content_'.$item['path']]['value']).'", NOW(),0, '.$item['id'].')');
			
			//}	
		}
		
		//print_r ($query);
		
		// multiple query TODO
		foreach ($query as $item) {
			$result = Db::query ($item);
			//print_r ($result);
			//if (!$result) { redirect ($result, $_POST['redirect'], "Błąd zapisu ! ".$item); }	
		}
		
		if ($result) redirect ($result, $_POST['redirect'], "Zmiany zostały zapisane");

    }		
}; // end class
?>