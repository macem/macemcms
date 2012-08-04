<?php

class Lang extends Db 
{
	
	/**
	 * method check if language exists
	 * @param $db - database: [mysql]
	*/
	function check ($lang, $current, $col, $default) {
		$result = Db::search ($lang, 'path', $current);
		
		if (count ($result)) {
			return $result[0][$col];
		} else {
			return $default;
		}
	}
	
	function get() {
		return $GLOBALS['config']['lang'];
	}

	function getLang ($id) {
		return Db::search ($GLOBALS['config']['lang'], 'id', $id);
	}	
	
	// $current STRING ['pl']
	// $langs table from db query table 'langs'
	function render ($current, $langs) {
		$data = '';//array();
		
		while ($item = array_shift ($langs)) {
			$url = url (true, null, ($item['id']!=$current ? $item['path'] : null) );
			$class = ($item['id']==$current ? 'current '.$item['path'] : $item['path']);

			//array_push ($data, array ('url' => $url, 'class' => $class, 'text' => $item['path']));
			if ($item['id']==$current) {
				$data .= '<strong class="'.$class.'" title="wybrany język">'.$item['path'].'</strong>';				
			} else {
				$data .= '<a href="'.$url.'" class="'.$class.'" title="wybierz język">'.$item['path'].'</a>';				
			}
	    }
		
		return $data;	
	}
	
	function add ($data) {
		allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);	
		
		$lang = Lang::getLang ($data['id']);
		
		if (!isset ($lang[0]['path'])) {
			$result = Db::query ( 'insert into lang(id, path) VALUES("'.$data['id'].'", "'.$data['path'].'")' );
		} else {
			$result = 'Język o tym id='.$data['id'].' już istnieje.';	
		}
		
		//print_r($result);
		
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
	
    function form ($id) {
        $token = allowMethod ('GET', 'Method Not Allowed', null, true);

		
        if ($id == 'new') { // TODO
            return '
            <form class="form-add-lang ajax-replace ajax-sel#tab-languages" action="'.url (true).'lang.save" method="post">
            <fieldset>
            <legend>Dodaj język</legend>
            <strong class="required">* - pole wymagane</strong>
            <input type="hidden" name="redirect" value="'.url (true).'#tab-languages!"/>
            <input type="hidden" name="item" value="'.$id.'"/>
			<input type="hidden" name="token" value="'.$token.'"/>
			
			<div><label for="id">Id *</label><input type="text" name="id" id="id" class="short" maxlength="3"/></div>
            <div><label for="path">Path *<em>(2-3 znaki)</em></label><input type="text" name="path" id="path" class="short" maxlength="3"/></div>
			<p class="important">Dostępne: pl, en, de, cz, fr, ru, jp</p>
            <div class="submit"><input type="submit" value="Dodaj" name="submit"/> lub <a href="'.url (true).'#tab-languages!" class="cancel">anuluj</a></div>
            
			</fieldset>
            </form>';

        } else {
            $lang = Lang::getLang ($id);

            return '
            <form class="form-add-lang ajax-replace ajax-sel#tab-languages" action="'.url (true).'lang.save" method="post">
            <fieldset>
            <legend>Edytuj język</legend>
            <strong class="required">* - pole wymagane</strong>
            <input type="hidden" name="redirect" value="'.url (true).'#tab-languages!"/>
            <input type="hidden" name="item" value="'.$id.'"/>
			<input type="hidden" name="token" value="'.$token.'"/>
			
			<div><label for="id">Id *</label><input type="text" name="id" id="id" class="short" readonly="readonly" maxlength="3" value="'.$id.'"/></div>
            <div><label for="path">Path *<em>(2-3 znaki)</em></label><input type="text" name="path" id="path" class="short" maxlength="3" value="'.$lang[0]['path'].'"/></div>
            <p class="important">Dostępne: pl, en, de, cz, fr, ru, jp</p>
			<div class="submit"><input type="submit" value="Zapisz zmiany" name="submit"/> lub <a href="'.url (true).'#tab-languages!" class="cancel">anuluj</a></div>
            
			</fieldset>
            </form>';
        }
    }	
		
}; // end class

?>