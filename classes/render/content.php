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
 
	 $content = array(); //'<h3 class="head"><a href="'.url (true).'blog.form?item=new" class="add button">dodaj artykuł</a></h3>';
 
	 $data = Db::search ($data, 'lang', $GLOBALS['config']['langid']);

	 foreach ($data as $key => $article) {
	         $item = array();

	         $item = $article;
	         
	         $user = Db::search ($GLOBALS['users'], 'id', $item['author']);
	         $item['author'] = $user[0]['login'];

	         $item['params'] = getParam ($article['params']);

	         $item['edit_url'] = url (true).'module.formarticle?item='.$article['uniq'];
	         $item['remove_url'] = url (true).'module.confirm?item='.$article['uniq'].'&text=delete this article&action=module.removearticle';

	         if (isset ($item['params']['module'])) {
		  require (PHP_PATH.'classes/render/'.$item['params']['module'].'.php');
	         }

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
			$content = Db::search ($data, 'uniq', $data[0]['uniq'], 'lang', $item['id']);
			
			$property .= Controls::tabPane ('tab_'.$item['path'], ($index==$GLOBALS['config']['langid']?true:false));
			$property .= '<em class="custom">Modyfikowany : <strong>'.$content[0]['date'].'</strong></em>';
			$property .= Controls::input (array (label=>'Tytuł:', name=>'title_'.$item['path'], id=>'title_'.$table.$item['path'], maxlength=>'200', classes=>'sidy', value=>$content[0]['title']));	
			$property .= Controls::textarea (array(label=>'Treść:', name=>'content_'.$item['path'], id=>'content_'.$table.$item['path'], classes=>'editable', rows=>'23', value=>$content[0]['content'] ));
			$property .= Controls::tabPaneEnd();
			$index++;
			
			$tabs['tab_'.$item['path']] = strtoupper ($item['path']);
		}
				
        return editor ($data, url ($table).'/page.savecontent?edit', 'Edycja', Controls::tabs ($tabs, $GLOBALS['config']['langid'], 'Wersja językowa').$property);
	} */    

}; // end class

?>