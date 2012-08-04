<?php

/**
 * class Module Page
 * @author macem
 * @package macemCMS
*/

require_once (PHP_PATH.'classes/blogs.php');

class Module extends Db
{

	/**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	function action ($name, $data) {
         switch ($name) {
			 case 'form':
                 return Blogs::form (Db::escape ($_GET['item']));
                 break;
             
			 case 'save':
                 if ($_GET['item'] == 'new') {
					Blogs::add (Db::valid());
                 } else {
                    Blogs::update (Db::valid(), Db::escape ($_GET['item']));
                 }
                 break;

             case 'remove':
                 Blogs::remove (Db::escape ($_GET['item']));
                 break;

         }
	}



};
?>