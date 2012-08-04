<?php

/**
 * class Module Content
 * @author macem
 * @package macemCMS
*/

class Module extends Db
{

	/**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	function view ($data) {
		
        $content = array();
        
        /*$content = '<h3 class="head"><a href="'.url (true).'blog.form?item=new" class="add button">Dodaj post</a></h3>';
		
		$data = Db::search ($data, 'lang', $GLOBALS['config']['langid']);
		
		foreach ($data as $key => $article) {
		 	if ($article['title']) {
				$content .= '<h2 class="title">'.$article['title'].'</h2>';
				$content .= '<a href="'.url (true).'blog.form?item='.$article['uniq'].'" class="edit">Edytuj</a> <a href="'.url (true).'blog.remove?item='.$article['uniq'].'" class="delete">Usuń</a>';
				$content .= '<span>Dodany '.$article['date'].'</span>';
			} 
			$content .= $article['content'];	
		}*/
        
		$data = Db::search ($data, 'lang', $GLOBALS['config']['langid']);
		        
		foreach ($data as $key => $article) {
		 	$item = array();
            
            $item = $article;
          
            $item['params'] = getParam ($article['params']);
             
            if (isset ($item['params']['plugins'])) {
                $item['plugins'] = Plugins::get ($item['params']['plugins']);
            }           
            
            if (isset ($item['params']['module'])) {
                require (PHP_PATH.'classes/render/'.$item['params']['module'].'.php');
            }
            
			$item['edit_url'] = url (true).'blog.form?item='.$article['uniq'];
            $item['remove_url'] = url (true).'blog.remove?item='.$article['uniq'];
            
            array_push ($content, $item);	
		}        
		
		return $content;
	}

	// TODO create API for view/edit mode
	/*function edit ($data) {

		$langs = Lang::get();
		$property = '';
		$tabs = array();
		$index = 0;
		$table = $GLOBALS['config']['table'];
		
		foreach ($langs as $key => $item) {
			$content = Db::search ($GLOBALS['content'], 'table_name', $table, 'lang', $item['id']);
			
			$property .= Controls::tabPane ('tab_'.$item['path'], ($index==$GLOBALS['config']['langid']?true:false));
			$property .= '<em class="custom">Modyfikowany : <strong>'.$content[0]['date'].'</strong></em>';
			$property .= Controls::input (array (label=>'Tytuł:', name=>'title_'.$item['path'], id=>'title_'.$table.$item['path'], maxlength=>'200', classes=>'sidy', value=>$content[0]['title']));	
			$property .= Controls::textarea (array(label=>'Treść:', name=>'content_'.$item['path'], id=>'content_'.$table.$item['path'], classes=>'editable', rows=>'23', value=>$content[0]['content'] ));
			$property .= Controls::tabPaneEnd();
			$index++;
			
			$tabs['tab_'.$item['path']] = strtoupper ($item['path']);
		}
				
        return editor ($data, url ($table).'/page.savecontent?edit', 'Edycja', Controls::tabs ($tabs, $GLOBALS['config']['langid'], 'Wersja językowa').$property);
	}*/     

}; // end class

?>