<?php

/**
 * class Util
 * @author macem
 * @package macemCMS
*/

function randomnumber ($liczb) {
    list($usec,$sec) = explode(" ", microtime());
    mt_srand( ( ((float)$sec+(float)$usec) * $liczb ) );
    
    return mt_rand();
}
function checkmail ($tekst) {
	
    if (!preg_match ( '/^([\w\.\-]){2,25}@([\w]){2,25}\.([\w]){2,8}$/', $tekst)){
        return false;
    } // end if

   return true;
}
     	
function checkurl ($tekst)  {
		
    if ($tekst != 'http://')		{
        if (!preg_match ( '/^(http|ftp|https):\/\/([\w\.\-_~]){3,}$/', $tekst)){
            return false;
        }
    }
        
    return true;			      
}
   
function cleanHTML ($tekst)  {

    $tekst = preg_replace('/ (onclick|onfocus|onblur|onmouseover|onmouseout|onload|onunload|ondblclick|onmousedown|onmouseup|onmousemove|onkeypress|onkeydown|onkeyup|onchange|onsubmit|onreset|onselect|oncontextmenu|onabort|onerror|onclose|onresize|onscroll|oncommand)=/s', 'js=', $tekst);

    return $tekst; 
}

function editVariable ($mode, $label, $variableName, $redirect) {
    $display = ($mode==true ? 'wysiwyg' : 'form');
    $variable = variableGet ($variableName);
    
    if (isset ($_SESSION['loggedin'])) {
        echo '<a href="'.url (true).'util.'.$display.'property?variable='.$variableName.'" id="property-'.$variableName.'" class="property" title="Edytuj">'.$label.'</a>';	
        echo $variable['value'];
    } else {
        echo $variable['value'];
    }
}

function variableGet ($variableName, $langId=null) {
    $lang = ($langId ? $langId : $GLOBALS['config']['langid']);
    
    $variable = Db::search ($GLOBALS['variables'], 'name', $variableName, 'lang', $lang);
    
    if (count ($variable)) { 
        return $variable[0]; 
    } else {
        return null;
    }			
}

// render page menu
function menu ($pages, $config) {
    $html = '';
	
    while ($item = array_shift ($pages)) {
        if (!$item[parentId]) {
            $names = explode (',', $item['name']);
            $html .= '<li'.($config['table']==$item['table_name']?' class="current"':'').'><a href="'.url ($item['table_name']).'" title="'.($config['table']==$item['table_name']?'aktualnie czytane':'czytaj').'">'.$names[$config['langid']].'</a></li>';
        }
    }
	
    return $html; 
}
function menuFooter ($pages, $langid) {
    $html = '';
    
    foreach ($pages as $key => $item) {
        $params = getParam ($item['params']);
        $names = explode (',', $item['name']);
        if ($params['addfooter'] == 'on') {
            $html .= '<a href="'.HOST.$item['table_name'].'">'.$names[$langid].'</a>';
        }
    } 
    
    return $html; 	
}

// something like fast OR in javascript
function _def ($value, $default) {
    if ($value != '') {
        return $value;
    } else {
        return $default;
    }	
}
function _if ($value, $default, $custom) {
    if ($value == true) {
        if ($custom) { return $custom; } else { return $value; }
    } else {
        return $default;
    }	
}

function _define ($name) {
    $data = Db::search ($GLOBALS['define'], 'name', $name);
    
    return $data[0]['value'];
}

// create url
// $lang STRING ['pl'] - is not render if is configured as default LANGUAGE and LANGUAGE_PATH
function url ($last='', $view=null, $lang=null) {
    $url  =  HOST;
    
    $language = _def ($lang, $GLOBALS['config']['langpath']);

    if ($lang || $GLOBALS['config']['langid'] != LANGUAGE) {
        if ($lang != LANGUAGE_PATH) $url .= $language.'/';
    }

    if ($view) {
        $url .= $view.'/';
    }

    if ($last === true) {
		$last = str_replace ($GLOBALS['config']['langpath'].'/', '', $GLOBALS['config']['path'].'/'); // DONT CHANGE
        if ($GLOBALS['config']['method']) $last = str_replace ('/'.$GLOBALS['config']['action'].'.'.$GLOBALS['config']['method'], '', $last);
		//echo $GLOBALS['config']['langpath'].'/:'.$GLOBALS['config']['path'].'/'.'<br>';
    }
    
    //echo $url.'<br>';
    
    return $url.$last;
}

// redirect 
// $result from DB query
// $redirect is url
function redirect ($result, $redirect, $message) {
    if (is_string ($result)) {
        header ("Location: ".message ($redirect, $result, true));
    } else {
        header ("Location: ".message ($redirect, $message));
    }
}

// display on page message, store in session
function message ($url, $message, $error=false, $step=2) {
    //$mode = ($error==true ? 'error=' : 'info=');
    //return $url.(strpos($url,'?')?'&'.$mode:'?'.$mode).urlencode ($message);
    if ($error == true) {
	    //$_SESSION['error'] = $message;
	    setStatus ($message, 'error', $step);		
    } else {
	    //$_SESSION['info'] = $message;
	    setStatus ($message, 'info', $step);		
    }

    return $url;
}

/**
* set status in session
*/
function setStatus ($status, $mode, $step=2) {
    $_SESSION['status_step'] = $step;
    $_SESSION[$mode] = $status;
    //print_r('set');
}

function clearStatus () {
    if ($_SESSION['status_step'] <= 0) { return false; }
    $_SESSION['status_step'] = $_SESSION['status_step'] - 1;
    
    if ($_SESSION['status_step'] <= 0) {
        $status = array (
            error => $_SESSION['error'],
            info  => $_SESSION['info']
        );
        
        $_SESSION['error'] = null;
        $_SESSION['info'] = null;
        
        return $status;
    }
}

function renderData ($from, $id, $item, $data) {

    $html = '';
    
    foreach ($from as $field_key => $field) { // newly added fields are displayed in forms

       $field_name = array_shift (explode(':', $field_key));
       
       $field_data = $data[$field_key];

       if (array_key_exists ($field_name, $GLOBALS['config']['field'])) {
          $name  = 'field_'.$item['path'].'_'.$id.'_'.$field_key.'_'.$field_data['params']['order'];

          /*$field = array(
	    'label'    => $from[$field_key][1],
	    'classes'  => $from[$field_key][2],
	    'size'     => $from[$field_key][3],
	    'disabled' => $from[$field_key][4],
	    'value'    => $field_data['value']
          );*/
          $field = Field::render ($from, $field_key, $field_data['value']);

          $html .= $GLOBALS['config']['field'][$field_name]( $name, $item, $field);
       }
    }
    
    return $html;
}

function renderFields ($from, $lang, $data) {

    $html = '';
    
    foreach ($from as $field_key => $field) {

       $field_name = array_shift (explode(':', $field_key)); // name:uniq
        
       if (array_key_exists ($field_name, $GLOBALS['config']['field'])) {
          $name  = $field_key.'_'.$lang['path'];

          /*$field = array(
            'label'    => $from[$field_key][1],
            'classes'  => $from[$field_key][2],
            'size'     => $from[$field_key][3],
            'disabled' => $from[$field_key][4],
            'value'    => $data[$field_key]
          );*/
          $field = Field::render ($from, $field_key, $data[$field_key]);

          $html .= $GLOBALS['config']['field'][$field_name]( $name, $lang, $field);
       }
    }
    
    return $html;
}
function renderParams ($from, $data) {

    $html = '';
    
    foreach ($from as $field_key => $field) { // newly added fields are displayed in forms

       $field_name = array_shift (explode(':', $field_key));
       $data_name = array_pop (explode(':', $field_key));
       
       $field = $data[$data_name];

       if (array_key_exists ($field_name, $GLOBALS['config']['field'])) {
          $name  = $field_key;

          /*$field = array(
	    'label'    => $from[$field_key][1],
	    'classes'  => $from[$field_key][2],
	    'size'     => $from[$field_key][3],
	    'disabled' => $from[$field_key][4],
	    'value'    => $field //$field['value']
          );*/
          $field = Field::render ($from, $field_key, $field);

          $html .= $GLOBALS['config']['field'][$field_name]( $name, $item, $field);
       }
    }
    
    return $html;
}
function setParams ($field_params, $data, &$where) {
    //$field_params = getAttr ($from);
    
    foreach ($field_params as $field_key => $field) { // newly added fields are displayed in forms

        $field_name = array_shift (explode(':', $field_key));
        $data_name = array_pop (explode(':', $field_key));
    
        if (isset ($data[$field_key]['value'])) {
            $where[$data_name] = $data[$field_key]['value'];
        }
    }
    
    //var_dump ($where);
} 

function getParam ($string) {
        $arr = array();
        parse_str ($string, $arr);
        return $arr;
}
function setParam ($array) {
        return http_build_query ($array);
}
function getAttr ($param) {
    
    $field_params = array();

    foreach (explode ('|', $param) as $field) {
        $value = explode ('=', $field);

        $temp = explode ('-', $value[0]);
        if ($temp[0]) {
            $field_params[$temp[0]] = $temp;
            $field_params[$temp[0]]['value'] = $value[1];
        }
    }

    return $field_params;
}

function setBodyClass ($classes) {
    $GLOBALS['config']['bodyClass'] .= ' '.$classes;
}

function editor ($data, $action, $title, $controls, $classes) {
    return '<form class="'._def($classes, 'form-save').'" action="'.$action.'" method="post">
    <fieldset>
    <legend>'.$title.'</legend>
    <div class="submit custom">
    <input type="submit" title="Zapisz wszystkie języki" value="Zapisz"/><!-- lub <a href="'.url(true).'" class="cancel">anuluj</a>--></div>
    <input type="hidden" name="redirect" value="'.url($data[0]['table_name'], _def(0,null)).'"/>
    <!--<input type="hidden" name="lang" value="'.$data[0]['lang'].'"/>-->
    '.Controls::hidden (array (name=>'uniq', value=>$data[0]['uniq'])).'
    <input type="hidden" name="new" value="'.$data[0]['new'].'"/>
    '.$controls.'
    </fieldset></form>';
}

// valid request -> TODO params
function allowMethod ($method, $message, $token=null, $logged=false) {
    //$_SESSION['token'] = null;
    if ($logged == true && !isset ($_SESSION['loggedin'])) {
        //$message = "No access, sorry!.";
        //header ('HTTP/1.1 405 '.$message);
        //header ('Content-Type: text/plain');
        //return $message; 
        //exit;
        
        //$config['template'] = 'view/login';
        echo "No access, sorry!.";
        //echo loginForm(url(true));
        exit;		
    }

    if ($method != $_SERVER['REQUEST_METHOD']) {
        $_SESSION['token'] = null;
        
        header ('Allow: '.$method);
        header ('HTTP/1.1 405 '.$message);
        header ('Content-Type: text/plain');
        echo $message; //"Method Not Allowed";
        exit;
    } else if ($token != null && $token != $_SESSION['token']) {
        $_SESSION['token'] = null;
        
        header ('Do not try like this: '.$method);
        header ('HTTP/1.1 405 '.$message);
        header ('Content-Type: text/plain');
        echo 'incorrect session';
        exit;			

    } else {
        $token = randomnumber (100000000000000000000000000000000);
        $_SESSION['token'] = $token;
    }
    
    return $token;	
}

?>