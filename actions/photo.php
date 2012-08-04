<?php

require_once (PHP_PATH.'classes/photos.php');

class Module extends Db
{

	/**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	function action ($name, $data) {
         switch ($name) {
             
            case 'form':
                return Photos::form (Db::escape ($_GET['item']), Db::escape ($_GET['path']));
                break;

            case 'add':
                return Photos::add (array (
                                  'photo'  => Db::escape ($_POST['photo']),
                                  'filter' => Db::escape ($_POST['filter']),
								  'redirect' => Db::escape ($_POST['redirect']),
								  'answer' => Db::escape ($_POST['answer']),
								  'MAX_FILE_SIZE' => Db::escape ($_POST['MAX_FILE_SIZE'])								  
                            ), $_POST['path'], $_POST['thumbnail']);
                break;

            case 'remove':
                return Photos::remove ($_POST['file'], $_POST['thumbnail']);
                break;

			case 'deleteFolder':
				return Photos::deleteFolder ($_POST['path']);
				break;
			
			case 'addFolder':
				return Photos::addFolder ($_POST['path']);
				break;				
								 
			case 'files':
			 	return Photos::getFiles (Db::escape ($_GET['item']));
				break;
				
			case 'folders':
			 	return Photos::getFolders (Db::escape ($_GET['item']));
				break;
				
			case 'exists':
				return Photos::exists (Db::escape ($_GET['path']));
				break;							 
         }
	}

}; // end class
?>