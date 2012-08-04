<?php

/**
 * class Module Contact
 * @author macem
 * @package macemCMS
*/

//class Module extends Db
//{
	/**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/

    $item['edit_url'] = url (true).'module.formemailer?item='.$article['uniq'];
    $item['remove_url'] = url (true).'module.removeemailer?item='.$article['uniq'];
        
	$item['content'] = '<form class="form-send" action="'.url (true).'util.send" method="post">
        <fieldset>
        <legend>Wyślij zapytanie</legend>
		<input type="hidden" name="redirect" value="'.url (true).'"/>
        <div><label for="user">Imię i Nazwisko *</label><input type="text" name="user" id="user" maxlength="100"/></div>
        <div><label for="phone">Email, numer telefonu</label><input type="text" name="phone" id="phone" maxlength="100"/></div>
        <div><img src="'.EDIT_CDN.'img/question.gif" align="left"/><input type="text" name="answer" id="answer" maxlength="1"/></div>
        <div><label for="question">Pytanie *</label><textarea name="question" id="question" maxlength="500" rows="5"></textarea></div>
        <div class="submit"><input type="submit" value="Wyślij"/></div>
        </fieldset>
        </form>';


	/*function view ($data) {
        
        $content = array(); //'<h3 class="head"><a href="'.url (true).'blog.form?item=new" class="add button">dodaj artykuł</a></h3>';
		
		$data = Db::search ($data, 'lang', $GLOBALS['config']['langid']);
		        
		foreach ($data as $key => $article) {
		 	$item = array();
            
            if ($article['title']) {
				//$content .= '<h2 class="title">'.$article['title'].'</h2>';
                $item['title'] = $article['title'];
			} 
			$item['edit_url'] = url (true).'blog.form?item='.$article['uniq'];
            $item['remove_url'] = url (true).'blog.remove?item='.$article['uniq'];
            //$content .= '<a href="'.url (true).'blog.form?item='.$article['uniq'].'" class="edit">edit</a>,<a href="'.url (true).'blog.remove?item='.$article['uniq'].'" class="delete">remove</a>';
			
			$item['content'] = $article['content'];
            
            array_push ($content, $item);	
		}
		
		return $content;
	}*/
    
	/**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	/*function view ($data) {
		
		$data = Db::search ($data, 'lang', $GLOBALS['config']['langid']);
		
		return $this->email().'<div class="pane-first">'.$data[0]['content'].'</div>';
	}*/
	
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
	}*/  	

//}; // end class

?>
