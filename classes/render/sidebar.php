<?php

/**
 * class Module Content
 * @author macem
 * @package macemCMS
*/

class Sidebar extends Db
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

			$item['params'] = getParam ($article['params']);
			
			$user = Db::search ($GLOBALS['users'], 'id', $item['author']);
			$item['author'] = $user[0]['login'];			
			
			$item['edit_url'] = url (true).'module.formarticle?item='.$article['uniq'];
			$item['remove_url'] = url (true).'module.confirm?item='.$article['uniq'].'&text=delete this article&action=removearticle';			

			if (isset ($item['params']['module'])) {
				require (PHP_PATH.'classes/render/'.$item['params']['module'].'.php');
			}

			array_push ($content, $item);	
		}

		return $content;
	}
   

}; // end class

?>