<?php

/**
 * class Pages
 * @author macem
 * @package macemCMS
*/

class Pages extends Db 
{
	// sort of pages
    public $labels;

    function __construct () {

    }

    /**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	function get () {
		return $GLOBALS['pages'];//Db::query ('select * from pages order by orders', true);
	}
	function getPage ($id, $by='id') {
		return Db::search ($GLOBALS['pages'], $by, $id);//Db::query ('select * from pages where id='.$id, true);
	}
	
	function add ($data) {
		allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);	
		
		$params = array();
		
		$params['access'] = _def ($data['access']['value'], 'false');
		$params['addfooter'] = _def ($data['addfooter']['value'], 'false');
		$params['addrightsidebar'] = _def ($data['addrightsidebar']['value'], 'false');
		if ($data['field-gallerypath'] != '') $params['field-gallerypath'] = $data['field-gallerypath']['value']; 

		$name = array();
		$langs = Lang::get();
		foreach ($langs as $key => $item) {
			array_push ($name, $data['name_'.$item['path']]['value']);
		}				
		
		$result  = Db::query ( 'insert into pages(name, table_name, orders, sort, status, params, parentId) VALUES("'.implode (',', $name).'","'.$data['table_name']['value'].'","'._def ($data['orders']['value'], 0).'","'.$data['sort']['value'].'", 0, "'.setParam ($params).'",'._def ($data['parentId']['value'],'NULL').')' );		

		redirect ($result, $_POST['redirect'], 'Strona "'.$name[0].'" została dodana.');	
	}
	
	function update ($data, $id) {
		allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);	
		
		$page = Pages::getPage ($id);
		
		$params = getParam ($page[0]['params']);
		
		$params['access'] = _def ($data['access']['value'], 'false');
		$params['addfooter'] = _def ($data['addfooter']['value'], 'false');
		$params['addrightsidebar'] = _def ($data['addrightsidebar']['value'], 'false');
		if ($data['field-gallerypath'] != '') $params['field-gallerypath'] = $data['field-gallerypath']['value']; 

		$name = array();
		$langs = Lang::get();
		foreach ($langs as $key => $item) {
			array_push ($name, $data['name_'.$item['path']]['value']);
		}	
				
		$result = Db::query ( 'update pages set name="'.implode (',', $name).'", table_name="'.$data['table_name']['value'].'", orders="'._def ($data['orders']['value'], 0).'", sort="'.$data['sort']['value'].'", status=0, params="'.setParam ($params).'", parentId='._def ($data['parentId']['value'],'NULL').' where id='.$id );		
		
		if ($page[0]['table_name'] !== $data['table_name']['value']) {
			$result = Db::query ('update content set table_name="'.$data['table_name']['value'].'" where table_name="'.$page[0]['table_name'].'"');
		}
		
		redirect ($result, $_POST['redirect'], 'Zmiany na stronie "'.$name[0].'" zostały zapisane.');	
	}	
	
	function remove ($id) {
		allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);
		
		$page = Pages::getPage ($id);
		
		$result = Db::query ('delete from pages where id='.$id);
		
		$result = Db::query ('delete from content where table_name="'.$page[0]['table_name'].'"');	
        
		redirect ($result, $_POST['redirect'], 'Strona "'.$page[0]['name'].'" została usunięta.');		
	}
	
	function sort ($sort) {
		return $GLOBALS['config']['pages'][$sort];
	}
    
    function form ($id) {
        $token = allowMethod ('GET', 'Method Not Allowed', null, true);

        if ($id == 'new') { // TODO

			//if ($GLOBALS['config']['sort'] == 1) {
				$adds = '<div><label for="field-gallerypath">Ścieżka do folderu <em>(* wymagane dla galerii)</em></label><input type="text" name="field-gallerypath" id="field-gallerypath" maxlength="60"/></div>';
				//$adds .= '<div><label for="field-contactto">email na który przesłać formularz <em>(* wymagane dla kontakt)</em></label><input type="text" name="field-contactto" id="field-contactto" maxlength="100"/></div>';
				//$adds .= '<div><label for="field-googlemap">adres google map <em>(* wymagane dla kontakt)</em></label><input type="text" name="field-googlemap" id="field-googlemap"/></div>';
			//}
			
	        foreach ($GLOBALS['config']['pages'] as $key => $item) {
	            $sortControl .= '<option value="'.$key.'">'.$item.'</option>';
	        }
			
			$tabs = Controls::tabByLang (Lang::get(), explode (',', $page[0]['name']), array(label=>'Nazwa <em>(* 4-60 znaków)</em>'), 'name_', 'path');
			
            return '
			<form class="form-add-page ajax-replace ajax-sel#tab-pages" action="'.url (true).'page.save" method="post">
            <fieldset>
            <legend>Dodaj stronę</legend>
            <strong class="required">* - pole wymagane</strong>
            <input type="hidden" name="redirect" value="'.url (true).'#tab-pages!"/>
            <input type="hidden" name="item" value="'.$id.'"/>
			<input type="hidden" name="token" value="'.$token.'"/>

			<ul class="tab-control">
			<li class="tab-show"><a href="#tab-page-main">podstawowe</a></li>
			<li><a href="#tab-page-advanced">zaawansowane</a></li>
			</ul>
			
			<div id="tab-page-main" class="tab-pane tab-show">			
            '.Controls::tabs ($tabs['tabs'], 0, array(classes=>'tab-small')).$tabs['html'].'
            <div><label for="table_name">Nazwa w adresie URL *<em>(A-Z, a-z, 0-9)</em></label><input type="text" name="table_name" id="table_name" maxlength="60"/></div>
            <div><label for="sort">Rodzaj *</label><select name="sort" id="sort" class="short">'.$sortControl.'</select></div>
			'.$adds.'
			<div><label><input type="checkbox" name="addfooter" id="addfooter" class="checkbox"/> dodaj w stopce strony</label></div>
			<div><label><input type="checkbox" name="addrightsidebar" id="addrightsidebar" class="checkbox" checked="checked"/> dodaj panel boczny prawy na stronie</label></div>
			
			</div>
			
			<div id="tab-page-advanced" class="tab-pane">
			<div><label><input type="checkbox" name="access" id="access" class="checkbox"/> dostęp tylko dla zalogowanych użytkowników</label></div>
			<div><label for="orders">Kolejność wyświetlania <em>(0-9)</em></label><input type="text" name="orders" id="orders" maxlength="3" value="0" class="small"/></div>
            <div><label for="parentId">Id rodzica strony</label><input type="text" name="parentId" id="parentId" maxlength="3" class="small"/></div>						
			</div>
						
            <div class="submit"><input type="submit" value="Dodaj" name="submit"/> lub <a href="'.url (true).'#tab-pages!" class="cancel">anuluj</a></div>
            </fieldset>
            </form>';

        } else {
            $page = Pages::getPage ($id);

			$params = getParam ($page[0]['params']);
			
			/*$adds = '';
			if ($params['field-gallerypath']) {
				foreach ($GLOBALS['config']['pages'] as $key => $item) {
					$adds = '<div><label for="field-gallerypath">Ścieżka do folderu <em>(* wymagane dla galerii)</em></label><input type="text" name="field-gallerypath" id="field-gallerypath" maxlength="60" value="'.$params['field-gallerypath'].'"/></div>';
				}
			}*/
				$adds = '<div><label for="field-gallerypath">Ścieżka do folderu <em>(* wymagane dla galerii)</em></label><input type="text" name="field-gallerypath" id="field-gallerypath" maxlength="60" value="'.$params['field-gallerypath'].'"/></div>';
				//$adds .= '<div><label for="field-contactto">email na który przesłać formularz <em>(* wymagane dla kontakt)</em></label><input type="text" name="field-contactto" id="field-contactto" maxlength="100"/></div>';
				//$adds .= '<div><label for="field-googlemap">adres google map <em>(* wymagane dla kontakt)</em></label><input type="text" name="field-googlemap" id="field-googlemap"/></div>';

			//}
			
	        foreach ($GLOBALS['config']['pages'] as $key => $item) {
	            $sortControl .= '<option value="'.$key.'"'.($key==$page[0]['sort']?' selected="selected"':'').'>'.$item.'</option>';
	        }

			$tabs = Controls::tabByLang (Lang::get(), explode (',', $page[0]['name']), array(label=>'Nazwa <em>(* 4-60 znaków)</em>'), 'name_', 'path');		
		
            return '
			<form class="form-add-page ajax-replace ajax-sel#tab-pages" action="'.url (true).'page.save" method="post">
            <fieldset>
            <legend>Edytuj stronę</legend>
            <strong class="required">* - pole wymagane</strong>
            <input type="hidden" name="redirect" value="'.url (true).'#tab-pages!"/>
            <input type="hidden" name="item" value="'.$id.'"/>
            <input type="hidden" name="token" value="'.$token.'"/>
			
			<ul class="tab-control">
			<li class="tab-show"><a href="#tab-page-main">podstawowe</a></li>
			<li><a href="#tab-page-advanced">zaawansowane</a></li>
			</ul>

			<div id="tab-page-main" class="tab-pane tab-show">
			'.Controls::tabs ($tabs['tabs'], 0, array(classes=>'tab-small')).$tabs['html'].'
            <div><label for="table_name">Nazwa w adresie URL *<em>(A-Z, a-z, 0-9)</em></label><input type="text" name="table_name" id="table_name" maxlength="60" value="'.$page[0]['table_name'].'"/></div>
            <div><label for="sort">Rodzaj *</label><select name="sort" id="sort" class="short">'.$sortControl.'</select></div>
			'.$adds.'
			<div><label><input type="checkbox" name="addfooter" id="addfooter" class="checkbox" '.($params['addfooter']=='on'?'checked="checked"':'').'/> wyświetl jako link w stopce strony</label></div>
			<div><label><input type="checkbox" name="addrightsidebar" id="addrightsidebar" class="checkbox" '.($params['addrightsidebar']=='on'?'checked="checked"':'').'/> wyświetl panel boczny prawy na tej stronie</label></div>
			
			</div>
			
			<div id="tab-page-advanced" class="tab-pane">

			<div><label><input type="checkbox" name="access" id="access" class="checkbox" '.($params['access']=='on'?'checked="checked"':'').'/> dostęp tylko dla zalogowanych użytkowników</label></div>
			<div><label for="orders">Kolejność wyświetlania <em>(0-9)</em></label><input type="text" name="orders" id="orders" maxlength="3" class="small" value="'.$page[0]['orders'].'"/></div>
            <div><label for="parentId">Id rodzica strony</label><input type="text" name="parentId" id="parentId" maxlength="3" class="small" value="'.$page[0]['parentId'].'"/></div>
			
			</div>
			
            <div class="submit"><input type="submit" value="Zapisz zmiany" name="submit"/> lub <a href="'.url (true).'#tab-pages!" class="cancel">anuluj</a></div>
            </fieldset>
            </form>';
        }
    }
    
    function saveContent ($data) {

        allowMethod ('POST', 'Method Not Allowed', null, true);

        if (!isset ($_SESSION['loggedin'])) {
            header ("Location: ".url(Db::escape ($_POST['redirect']))."?info=You have no permission!");
        }
		
		//cleanHTML (

		$query = array();
		$langs = Lang::get();
		$table = $GLOBALS['config']['table'];
		
		$unique = uniqid (); // unique id
		
		foreach ($langs as $key => $item) {
			$content = Db::search ($GLOBALS['content'], 'uniq', $data['uniq']['value'], 'lang', $item['id']);

			if ($content[0]['content']) {
				array_push ($query ,'update content set title="'.$data['title_'.$item['path']]['value'].'", content="'.cleanHTML ($data['content_'.$item['path']]['value']).'", date=NOW() where lang='.$item['id'].' && table_name="'.$table.'" && uniq="'.$data['uniq']['value'].'"');
			} else {
				array_push ($query ,'insert into content(uniq, title, table_name, content, date, status, lang) values("'.$unique.'", "'.$data['title_'.$item['path']]['value'].'","'.$table.'", "'.cleanHTML ($data['content_'.$item['path']]['value']).'", NOW(),0, '.$item['id'].')');
			
			}	
		}
		
		//print_r ($query);
		
		// multiple query TODO
		foreach ($query as $item) {
			$result = Db::query ($item);
			if (!$result) redirect ($result, $_POST['redirect'], "Błąd zapisu ! ".$item);	
		}
		
		if ($result) redirect ($result, $_POST['redirect'], "Zmiany zostały zapisane");
				
        /*if (isset ($data['text'])) {

            if ($_POST['new'] != 'true') {
                $result = Db::query ('update content set content="'.$data['text'].'", date=now() where lang='.Db::escape ($_POST['lang']).' && title="'.Db::escape ($_POST['redirect']).'"');
            } else {
                $result = Db::query ('insert into content(title, content, date, status, lang) values("'.Db::escape ($_POST['redirect']).'", "'.$data['text'].'", NOW(),0, '.Db::escape ($_POST['lang']).')');
            }
			//print_r ('update content set content="'.$data['text'].'", date=now() where lang='.Db::escape ($_POST['lang']).' && title="'.Db::escape ($_POST['redirect']).'"');
            if ($result) {
                header ("location: ".url(Db::escape ($_POST['redirect']))."?info=zmiany zostały zapisane");
            } else {
                header ("location: ".url(Db::escape ($_POST['redirect']))."?info=błąd zapisu !");
            }
        } else {
            // todo $_post['text']
        }*/
    }
		
}; // end class
?>