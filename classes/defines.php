<?php

class Define extends Db 
{
	
	function get() {
		return $GLOBALS['define'];
	}

	function getDefine ($name) {
		return Db::search ($GLOBALS['define'], 'name', $name);
	}	
	
	function add ($data) {
		allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);	
		
		$result  = Db::query ( 'insert into lang(id, path) VALUES("'.$data['id'].'", "'.$data['path'].'")' );		

		redirect ($result, $_POST['redirect'], 'Język "'.$data['path'].'" został dodany.');	
	}
	
	function update ($data, $id) {
		allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);	
		
		$result = Db::query ( 'update lang set id="'.$data['id'].'", path="'.$data['path'].'" where id='.$id );		
		
		redirect ($result, $_POST['redirect'], 'Zmiany dla języka "'.$data['path'].'" zostały zapisane.');	
	}	
	
	function remove ($id) {
		allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);	
		
		$lang = Lang::getLang ($id);
		
		$result = Db::query ('delete from lang where id='.$id);	
        
		redirect ($result, $_POST['redirect'], 'Język "'.$lang[0]['path'].'" został usunięty.');		
	}	
	
		
}; // end class

?>