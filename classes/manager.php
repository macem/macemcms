<?php

/**
 * class Module Content
 * @author macem
 * @package macemCMS
*/

require_once (PHP_PATH.'classes/images.php');

class Module extends Db
{

	/**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	function view ($data) {
		$content = '<div id="leftpane" ></div><div class="middlepane"></div>';

		
		return $content;
	}

	// TODO create API for view/edit mode
	function edit ($data) {
	
	}     

}; // end class

?>