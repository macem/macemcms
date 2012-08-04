<?php

/**
 * class connection
 * @author macem
 * @package cmp
*/

class Db
{
	/**
	* Private
	* $adaptee an instance of the DataAccess class
	*/
	public $db;

	/**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	function connect ($host, $user, $passw, $dbname, $dbs = 'mysql')	{

		switch ($dbs) {
			case 'mysql':
			$this->db = mysql_connect ($host, $user, $passw) or die (mysql_error());
			
			mysql_select_db ($dbname) or die (mysql_error());
			mysql_query("SET NAMES 'utf8'");
			//mysql_query ("SET NAMES 'latin2'");
			//mysql_query("SET CHARACTER SET 'utf8_polish_ci'");
			break;
		}
	}
	
	function get () {
		return $this->db;
	}

	/**
	 * method disconnect from database
	 * @noparam
	*/
	function disconnect() {

		mysql_close ($this->db)
		or die (errors::getvar (errors::geterror (4, mysql_error()), 'comment'));
	}	

	/**
	 * method dd unikate sign like ' "
	 * @param
	*/
	function escape ($content) {
		$result = mysql_real_escape_string (str_replace(array("\r", "\n", "\t"), '', $content));

		return $result;
	}
	
	/**
	 * method dd unikate sign like ' "
	 * @param
	*/
	function html ($content) {	
		return htmlspecialchars ($content, ENT_QUOTES);
	}

	/**
	 * method send mysql query
	 * @param query
	 * @return array
	*/
	function query ($mysql, $mode=false) {
		
		$array = array();
		$i = 0;
		
		$result = mysql_query ($mysql);
	
		if (!$result) {
			return mysql_error();
		} else if (!count ($result) || !is_resource($result)) {
			return $result;
		}
		
		if ($mode == false) {
			while ($line = mysql_fetch_row ($result)) {  //mysql_fetch_object($result)
				array_walk ($line, 'strip_array');
				$array[$i++] = $line; 
			}
		} else {
			while ($line = mysql_fetch_assoc($result)) { 
				array_walk ($line, 'strip_array');
				$array[$i++] = $line; 
			}
		}
	
		mysql_free_result ($result);
		
		return $array;
	} 
	
	function getQuery ($id_query)	{
		
		global $query_list;
		
		return $query_list[$id_query];
	}
	
	function parseQuery() {
		
		$a = func_get_args();
		$format = array_shift($a);
		
		return vsprintf ($format, $a);
	}
	
	/**
	 * method search table and return rows
	 * @param
	 * @return array
	*/
	function search (&$table, $col, $value = null, $col1 = 0, $value1 = null) {

		$result = array ();
		$len = count ($table);
		for ($i = 0; $i < $len; $i++)	{
			if ($table[$i][$col] == $value/* && $value != ""*/) {
				if ($col1) {
						if ($table[$i][$col1] == $value1) {
							array_push ($result, $table[$i]);
						}
					} else {
					array_push ($result, $table[$i]);
				}
			}
		}
		
		return $result;
	}
	
	function getParams () {
		$arrParams = array ();
		$arrParamsx = array ();
		
		if (isset ($_SERVER['REQUEST_URI'])) {
			$arrParams = explode ( '/', substr( $_SERVER['REQUEST_URI'], 1) );
		}
		//print_r($arrParams);
		
		$len = count($arrParams);
		for ($i = PAGE_URL_START; $i < $len; $i++) {
			if ($arrParams[$i] != '') {
				$tmp = preg_split ('/[?,#]+/', $arrParams[$i]);
				if ($tmp['0'] != '') array_push ($arrParamsx, $tmp['0']);
			}
		}
		//print_r($arrParamsx);
		
		return $arrParamsx;
	}
	
	/**
	 * validate form
	 * field name xxx-required
	 *            xxx-password
	 *            xxx-
	*/
	function valid () {

		$data = array();
		$data['error'] = false;

		foreach ($_POST as $key => $value) {
			$item = array();
			$item = explode ('-', $key);
			$item['value'] = Db::escape ($value);

			switch ($item[1]) {

				case "required":
				if ($item['value'] == "") {
					$item['status'] = "This is required!";
					$data['error'] = true;
				}
				break;
	
				case "password":
				if ($item['value'] && $item[2] == "parse") {
					$len = strlen($item['value']);
					if ((int)$item[3] > $len || (int)$item[4] < $len) {
						$item['status'] = "Password should have ".$item[3]." to ".$item[4]." signs!";
						$data['error'] = true;
					}
				}
				if ($item[2] == "compare") {
					//echo $item['value'].$data[$item[3]]['value'];
					if (strcmp ($item['value'], $data[$item[3]]['value'])) {
						$item['status'] = "Retype password here!";
						$data['error'] = true;
					}
				}
				break;
			}

			switch ($item[2]) {

				case "parse":
				if ($item['value'] && $item[3] == "number") {
					if (!is_numeric ($item['value'])) {
						$item['status'] = "This is not a number!";
						$data['error'] = true;
					} else if ($item[4]) {
						if (strlen($item['value']) != (int)$item[4]) {
							$item['status'] = "Number's length is too short!";
							$data['error'] = true;
						}
					}
				}
				break;
	
				case "check":
				if ($item['value'] && $item[3] == "email") {
					if(!preg_match( '/^([\w\.\-]){2,25}@([\w]){2,25}\.([\w]){2,8}$/', $item['value'])){
						$item['status'] = "Email is incorrect!";
						$data['error'] = true;
					}
				}
				if ($item['value'] &&  $item['value'] != "http://" && $item[3] == "url") {
					if(!preg_match( '/^(http|ftp|https):\/\/([\w\.\-_~]){3,}$/', $item['value'])){
						$item['status'] = "Url is incorrect!";
						$data['error'] = true;
					}
				}
				if ($item['value'] && $item[3] == "slug") {
					if(!preg_match( '/^([a-z,A-Z,0-9,-_~]){3,}$/', $item['value'])){
						$item['status'] = "Expression is incorrect!";
						$data['error'] = true;
					}
				}
				break;
			}
			$data[$item[0]] = $item;
		}
		
		if ($data['error'] == false) {
			unset ($data['error']);
		}

		return $data;
	}			

}; // end class

function strip_array (&$item1, $key) {
	$item1 = stripcslashes ($item1); //stripcslashes
}
?>