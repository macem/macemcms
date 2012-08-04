<?php

/**
 * class Module Page
 * @author macem
 * @package macemCMS
*/

class Module extends Db
{

	/**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	function action ($name, $data) {
         switch ($name) {
             case 'form':
                 return Lang::form (Db::escape ($_GET['item']));
                 break;
             
			 case 'save':
                 if ($_POST['item'] == 'new') {
					Lang::add (array (
                                      'id'   => Db::escape ($_POST['id']),
									  'path' => Db::escape ($_POST['path'])
                                ));
                 } else {
                    Lang::update (array (
                                      'id'   => Db::escape ($_POST['id']),
									  'path' => Db::escape ($_POST['path'])						  
                                ), $_POST['item']);
                 }
                 break;

             case 'remove':
                 Lang::remove ($_POST['item']);
                 break;

         }
	}



};
?>