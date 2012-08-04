<?php

/**
 * class Module Page
 * @author macem
 * @package macemCMS
*/

require_once (PHP_PATH.'classes/pages.php');

class Module extends Db
{

	/**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	function action ($name, $data) {
         switch ($name) {
             case 'savecontent':
                 Pages::saveContent (Db::valid());
                 break;
             
			 case 'form':
                 return Pages::form (Db::escape ($_GET['item']));
                 break;
             
			 case 'save':
                 if ($_POST['item'] == 'new') {
					Pages::add (Db::valid());
                 } else {
                    Pages::update (Db::valid()/*array (
                                      'name'  => Db::escape ($_POST['name']),
                                      'table_name' => Db::escape ($_POST['table_name']),
									  'orders' => _def (Db::escape ($_POST['orders']), '0'),
                                      'sort'  => _def (Db::escape ($_POST['sort']), '0'),
									  'parentId'  => _def (Db::escape ($_POST['parentId']), 'NULL'),
									  'access'  => _def (Db::escape ($_POST['access']), 'false'),
									  'addfooter'  => _def (Db::escape ($_POST['addfooter']), 'false'),
									  'addrightsidebar'  => _def (Db::escape ($_POST['addrightsidebar']), 'false'),
									  'field-gallerypath' => _def (Db::escape ($_POST['field-gallerypath']), '')									  
                                )*/, Db::escape ($_POST['item']));
                 }
                 break;

             case 'remove':
                 Pages::remove (Db::escape ($_POST['item']));
                 break;

         }
	}



};
?>