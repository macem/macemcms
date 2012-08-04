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
            
            //if ($article['title']) {
				//$content .= '<h2 class="title">'.$article['title'].'</h2>';
                //$item['title'] = $article['title'];
			//} 
            
            $item['params'] = getParam ($article['params']);
            
            if (isset ($item['params']['module'])) {
                require (PHP_PATH.'classes/render/'.$item['params']['module'].'.php');
            }
            
            //$item['date'] = $article['date'];
            
			$item['edit_url'] = url (true).'blog.form?item='.$article['uniq'];
            $item['remove_url'] = url (true).'blog.remove?item='.$article['uniq'];
            //$content .= '<a href="'.url (true).'blog.form?item='.$article['uniq'].'" class="edit">edit</a>,<a href="'.url (true).'blog.remove?item='.$article['uniq'].'" class="delete">remove</a>';
			
			//$item['content'] = $article['content'];
            
            array_push ($content, $item);	
		}
		
		return $content;
	}

}; // end class

?>