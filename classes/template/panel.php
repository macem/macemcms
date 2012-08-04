<?php

/**
 * class Module Panel
 * @author macem
 * @package macemCMS
*/

require_once (PHP_PATH.'classes/users.php');
require_once (PHP_PATH.'classes/pages.php');
require_once (PHP_PATH.'classes/defines.php');

class Module extends Db
{
	function __construct () {
		$GLOBALS['config']['bodyClass'] .= ' panel'; // TODO write method
	}

    function users () {
        $users = Users::get();

        $html  = '<div id="tab-users" class="tab-pane tab-show">
        <h3 class="head">Lista użytkowników: <a href="'.url(true).'user.form?item=new" class="add button">dodaj użytkownika</a></h3>
        <ol class="list">';

        while ($item = array_shift ($users)) {
            $html .= '<li><strong>'.$item['login'].'</strong>, <span><a href="'.url (true).'user.password?item='.$item['id'].'" class="password">zmiana hasła</a>, <a href="'.url (true).'user.form?item='.$item['id'].'" class="edit">edytuj</a>, <a href="'.url (true).'util.formdelete?action=user.remove&info=user%20'.$item['login'].'&item='.$item['id'].'&replace=%23tab-users" class="delete">usuń</a></span>';
            $html .= '<em>nieudane logowania: <strong>'.$item['fail'].'/(3)</strong>, email: <strong>'.$item['email'].'</strong>, dodany: <strong>'.$item['date'].'</strong></em></li>';
        };
        $html .= '</ol></div>';

        return $html;
	}

    function pages () {
        $pages = Pages::get();
        $html  = '<div id="tab-pages" class="tab-pane">
        <h3 class="head">Lista stron: <a href="'.url (true).'page.form?item=new" class="add button">dodaj stronę</a></h3>
        <ol class="list">';

        while ($item = array_shift ($pages)) {
            $html .= '<li><strong>'.array_shift (explode (',', $item['name'])).'</strong>, <span><a href="'.url (true).'page.order?item='.$item['id'].'" class="order">przesuń</a>, <a href="'.url (true).'page.form?item='.$item['id'].'" class="edit" title="edytuj stronę">edytuj</a>, <a href="'.url (true).'util.formdelete?action=page.remove&info=page%20'.$item['name'].'&item='.$item['id'].'&replace=%23tab-pages" class="delete">usuń</a></span>';
            $html .= '<em>Id: <strong>'.$item['id'].'</strong> , rodzaj strony: <strong>'.Pages::sort ($item['sort']).'</strong></em></li>';
        };
        $html .= '</ol></div>';

        return $html;
	}
	
    function langs () {
        $langs = Lang::get();

        $html  = '<div id="tab-languages" class="tab-pane">
        <h3 class="head">Lista języków: <a href="'.url(true).'lang.form?item=new" class="add button">dodaj język</a></h3>
        <ol class="list">';

        while ($item = array_shift ($langs)) {
            $html .= '<li><strong>'.$item['path'].'</strong>, <span><a href="'.url (true).'lang.form?item='.$item['id'].'" class="edit">edytuj</a>, <a href="'.url (true).'util.formdelete?action=lang.remove&info=język%20'.$item['path'].'&item='.$item['id'].'&replace=%23tab-languages" class="delete">usuń</a></span>';
            $html .= '<em>Id: <strong>'.$item['id'].'</strong>, URL: '.HOST.$item['path'].'/</em></li>';
        };
        $html .= '</ol></div>';

        return $html;
	}
	
    function defines () {
        $defines = Define::get();

        $html  = '<div id="tab-defines" class="tab-pane">
        <h3 class="head">Lista zmiennych: <a href="'.url(true).'util.formdefine?item=new" class="add button">dodaj zmienną</a></h3>
        <ol class="list">';

        while ($item = array_shift ($defines)) {
            $html .= '<li><form method="post" action="'.url(true).'util.savedefine" class="ajax-replace ajax-id#tab-defines">
			<label><strong>'.$item['name'].'</strong></label> 
			<input type="hidden" name="redirect" value="'.url(true).'#tab-defines!"/>
			<input type="hidden" name="id" value="'.$item['name'].'"/>
			<input type="text" class="short" name="value" value="'.$item['value'].'"/> 
			<div class="submit inline"><input type="submit" value="Zapisz"/></div> </form>';
        };
        $html .= '</ol></div>';

        return $html;
	}	
	
    function statistics () {

        $html  = '<div id="tab-statistics" class="tab-pane">
		<h3 class="head">Statystyki:</h3>
		<a href="https://www.google.com/analytics/web/#report/visitors-overview/a25112539w48873826p49331353/">Google analitycs</a>
		</div>';

        return $html;
	}			

	/**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	function view ($data) {
        $panel = Pages::getPage (10, 'sort');
		
		$tabs = '<h2>'.$panel[0]['name'].'</h2><ul class="tab-control">
        <li class="tab-show"><a href="#tab-users">Użytkownicy</a></li>
        <li><a href="#tab-pages">Strony</a></li>
		<li><a href="#tab-languages">Języki</a></li>
		<li><a href="#tab-defines">Konfiguracja</a></li>
		<li><a href="#tab-statistics">Statystyki</a></li>
        </ul>';

        return $tabs.$this->users().$this->pages().$this->langs().$this->defines().$this->statistics();
	}

}; // end class

?>