<?php

/**
 * class Photos
 * @author macem
 * @package macemCMS
*/

require_once ('images.php');

class Photos extends Db 
{

	function getFiles ($path) {
		return Images::getFile ($path, 'files');
	}
	function getFolders ($path) {
		return Images::getFile ($path, 'folders');
	}
	function addFolder ($path) {
		return Images::addFolder ($path);
	}
	function deleteFolder ($path) {
		return Images::deleteFolder ($path);
	}	
	function exists ($path) {
		return Images::exists ($path);
	}	
	
	function add ($data, $path, $thumbnail) {
		allowMethod ('POST', 'Method Not Allowed', $_POST['token']);
		
		//if ($data['answer'] != 7) {
		//	$return = "Jedno z pól formularza jest niepoprawne, popraw formularz i wyślij ponownie";
		//} else if ($data['answer'] == 7) {
			
			$folder = getcwd().$path; //'/img/photos/';
			
			//mkdir ($folder, 0777);
			
			$return = Images::uploadFile ($folder, $data['filter'], $thumbnail);
			
			//chmod ('img/photos/', 0444);
		//}		
		
		//echo $return;
		
		//return $return;
		
		if (!isset ($_POST['redirect'])) { return $return; }
		
		if ($return !== true) {
			//if we have ajax we shouldnt redirect
			header ("Location: ".message ($_POST['redirect'], $return, true, 1), true, 500);
		} else {
			header ("Location: ".message ($_POST['redirect'], "Zdjęcie zostało dodane.", true, 1));
		}	
	}
	
	function update ($data, $id) {
		//allowMethod ('POST', 'Method Not Allowed');	
		
		//$result = Db::query ( 'update pages set name="'.$data['name'].'", table_name="'.$data['table_name'].'", orders="'.$data['orders'].'", sort="'.$data['sort'].'", parentId='.$data['parentId'].' where id='.$id );		
		
		//redirect ($result, $_POST['redirect'], 'Zmiany na stronie "'.$data['name'].'" zostały zapisane.');	
	}	
	
	function remove ($path, $thumbnail) {
		allowMethod ('POST', 'Method Not Allowed', $_POST['token']);
			
		$folder = getcwd().$path;//'/'.$_POST['file'];
		
		$return = Images::delete ($folder, $thumbnail);
		
		//chmod ('img/photos/', 0444);
		
		if (!isset ($_POST['redirect'])) { return $return; }
		
		if ($return !== true) {
			header ("Location: ".message ($_POST['redirect'], $return, true));
		} else {
			header ("Location: ".message ($_POST['redirect'], "Zdjęcie zostało skasowane."));
		}

	}
    
    function form ($id, $path) {
        $token = allowMethod ('GET', 'Method Not Allowed');
		
		//$path = $GLOBALS['config']['params']['field-gallerypath'];
			
        if ($id == 'new') { // TODO
			
            return '<form class="form-add-photo ajax-upload ajax-seldiv.gallery" enctype="multipart/form-data" action="'.url (true).'photo.add" method="post">
			<fieldset>
			<legend>Dodaj zdjęcie</legend>
			<strong class="required">* - pole wymagane</strong>
			<input type="hidden" name="redirect" value="'.url (true).'"/>
			<input type="hidden" name="path" value="'.$path.'"/>
			<input type="hidden" name="thumbnail" value="true"/>
			<input type="hidden" name="MAX_FILE_SIZE" value="500" />
			<input type="hidden" name="token" value="'.$token.'"/>
			<input type="hidden" name="check-exists" value="'.url (true, 'view-ajax').'photo.exists?path='.urlencode($path).'"/>
			
			<div><label for="photo">Oryginalne zdjęcie *</label><input type="file" name="photo" id="photo" size="50%"/><em>wielkość pliku do 500kb(kilobajtów) (akceptowany format .jpg, .gif, .png)</em></div>
			<div><label for="filter"><input type="checkbox" id="filter" name="filter" value="greyscale" class="checkbox"/> Miniaturka w odcieniach szarości ?</label></div>
			<div class="submit"><input type="submit" value="Dodaj" name="submit"/> lub <a href="'.url (true).'" class="cancel">anuluj</a></div>
			</fieldset>
			</form>';

        } else if ($id == 'delete') {
		
            return '<form class="form-delete-photo" action="'.url (true).'photo.remove" method="post">
			<fieldset>
			<legend>Czy chcesz usunąć zdjęcie</legend>
			<input type="hidden" name="redirect" value="'.url (true).'?edit"/>
			<input type="hidden" name="thumbnail" value="true"/>
			<input type="hidden" name="file" value="'.$_GET['file'].'"/>
			<input type="hidden" name="token" value="'.$token.'"/>
			
			<img src="'.HOST.$_GET['file'].'" alt=""/>
			<div class="submit"><input type="submit" value="Usuń" name="submit"/> lub <a href="'.url (true).'" class="cancel">anuluj</a></div>
			</fieldset>
			</form>';
        }
    }
		
}; // end class
?>