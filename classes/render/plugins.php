<?php

/**
 * class Module Content
 * @author macem
 * @package macemCMS
*/

class Plugins extends Db
{

	/**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	function get ($plugins) {
        
        $plugins = explode (';', $plugins);
        
        $data = array(); 
   
		foreach ($plugins as $key => $plugin) {
          
            $item = array();
            
            $item['params'] = explode (':', $plugin);
            
			switch ($item['params'][0]) {
                case 'comment':
                    $item['name'] = $item['params'][0];
                    $item['data'] = $item['params'][1];
                break;
                case 'rate': 
                    $item['name'] = $item['params'][0];
                    $item['data'] = $item['params'][1];
                break;   
            }
            
            array_push ($data, $item);	
		}
		
		return $data;
	}

}; // end class

?>