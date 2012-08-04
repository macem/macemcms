<?php

require_once (PHP_PATH.'classes/users.php');

class Module extends Db
{

	/**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	function action ($name, $data) {
         switch ($name) {
             
			 case 'login':
                 Users::login ();
                 break;

             case 'logout':
                 Users::logout ();
                 break;

             case 'form':
                 return Users::form (Db::escape ($_GET['item']));
                 break;

             case 'save':
                 if ($_POST['item'] == 'new') {
                    /*Users::add (array (
                                      'login'    => Db::escape ($_POST['login']),
                                      'password' => Db::escape ($_POST['password']),
									  'retype_password' => Db::escape ($_POST['retype_password'])
                                ));*/
					Users::add (Db::valid());			
                 } else {
                    Users::update (Db::valid(), Db::escape ($_POST['item']));
                 }
                 break;

             case 'password':
                 return Users::password (Db::escape ($_GET['item']));
                 break;

             case 'changepassword':
                 Users::setPassword (Db::valid(), Db::escape ($_POST['item']));
				break;
								
             case 'remove':
                 Users::remove (Db::escape ($_POST['item']));
                 break;
         }
	}

}; // end class
?>