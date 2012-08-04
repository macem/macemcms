<?php

/**
 * class Users
 * @author macem
 * @package macemCMS
*/

class Users extends Db 
{
	/**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	function get () {
        return Db::query ('select * from user', true);
	}

	function getUser ($id) {
        return Db::query ('select * from user where id='.$id, true);
	}
    
	function login () {
	    allowMethod ('POST', 'Method Not Allowed');

        $user  = Db::escape ($_POST['login']);
        $login = Db::query ('select * from user where login="'.$user.'"', true);

        if ($login[0] && $login[0]['fail'] > 2 ) {
            sleep (2);
			header ("Location: ".message ($_POST['redirect'], 'Login '.$user.' został zablokowany !'));

        } else if ($login[0] && md5 ($_POST['password'].PREFIX) == $login[0]['password']) {
            $_SESSION['loggedin'] = $user.PREFIX;
			$_SESSION['user'] = $user;
			$_SESSION['userid'] = $login[0]['id'];
            $result = Db::query ('update user set fail=0,date=NOW() where id='.$login[0]['id']);
            redirect ($result, $_POST['redirect'], 'Witamy w systemie "macemCMS", masz pytanie lub problem skorzystaj z linku Pomoc.');

        } else if ($login[0]){
            $result = Db::query ('update user set fail='.Db::escape ($login[0]['fail']+1).',date=NOW() where id='.$login[0]['id']);
            sleep (2);
			redirect ($result, $_POST['redirect'], 'Niepoprawne hasło !');
        } else {
            sleep (2);
			header ("Location: ".message ($_POST['redirect'], 'Niepoprawny login !'));
        }

        //setcookie ('login-attempt', $_POST['login'].':sztelmark'); //$_COOKIE['cookiename']*/
	}

	function logout () {
	    allowMethod ('GET', 'Method Not Allowed');

        session_unset();
        session_destroy();

        header ("Location: ".message ($_GET['redirect'], 'Użytkownik został wylogowany z systemu.'));
	}

    function form ($id) {
        $token = allowMethod ('GET', 'Method Not Allowed', null, true);

        if ($id == 'new') { // TODO
            return '
			<form class="form-add-user ajax-replace ajax-sel#tab-users" action="'.url (true).'user.save" method="post">
            <fieldset>
            <legend>Dodaj użytkownika</legend>
            <strong class="required">* - pole wymagane</strong>
            <input type="hidden" name="redirect" value="'.url (true).'#tab-users!"/>
            <input type="hidden" name="item" value="'.$id.'"/>
			<input type="hidden" name="token" value="'.$token.'"/>
			
			'.Controls::input (array (
				label=>'Login *<em>(A-Z, a-z, 0-9)</em>', 
				required=>'required', focus=>'autofocus', 
				name=>'login', max=>'20', 
				classes=>'short')).'
			'.Controls::input (array (
				label=>'E-mail', 
				name=>'email',
				type=>'email', 
				max=>'60', 
				classes=>'short', 
				placeholder=>'test@test.com')).'
			'.Controls::input (array (
				label=>'Hasło *<em>(6-20 znaków)', 
				name=>'password', 
				type=>'password', 
				required=>'required', 
				max=>'20', 
				classes=>'short')).'
			'.Controls::input (array (
				label=>'Powtórz hasło *', 
				name=>'retype_password', 
				type=>'password', 
				required=>'required', 
				max=>'20', 
				classes=>'short')).'
			
			'.Controls::submit (array (
				value=>'Dodaj', 
				url=>url (true).'#tab-users!')).'
            </fieldset>
            </form>';

        } else {
            $user = Users::getUser ($id);

            return '
			<form class="form-add-user ajax-replace ajax-sel#tab-users" action="'.url (true).'user.save" method="post">
            <fieldset>
            <legend>Edytuj użytkownika</legend>
            <strong class="required">* - pole wymagane</strong>
            <input type="hidden" name="item" value="'.$id.'"/>
            <input type="hidden" name="redirect" value="'.url (true).'#tab-users!"/>
			<input type="hidden" name="token" value="'.$token.'"/>
			
			'.Controls::input (array (
				label=>'Login *<em>(A-Z, a-z, 0-9)</em>', 
				required=>'required', focus=>'autofocus', 
				name=>'login', max=>'20', 
				classes=>'short')).'
			'.Controls::input (array (
				label=>'E-mail', 
				name=>'email',
				type=>'email', 
				max=>'60',
				value=>$user[0]['email'], 
				classes=>'short', 
				placeholder=>'test@test.com')).'			
			
			<p class="important">W przypadku zablokowanego konta, zostanie ono odblokowane.</p>
            <div class="submit"><input type="submit" value="Zapisz zmiany" name="submit"/> lub <a href="'.url (true).'#tab-users!" class="cancel">anuluj</a></div>
            </fieldset>
            </form>';
        }
    }

    function password ($id) {
        $token = allowMethod ('GET', 'Method Not Allowed', null, true);

        $user = Users::getUser ($id);

        return '
		<form class="form-password-user ajax-replace ajax-sel#tab-users" action="'.url (true).'user.changepassword" method="post">
        <fieldset>
        <legend>zmiana hasła użytkownika '.$user[0]['login'].'</legend>
        <strong class="required">* - pole wymagane</strong>
        <input type="hidden" name="item" value="'.$id.'"/>
        <input type="hidden" name="redirect" value="'.url(true).'#tab-users!"/>
		<input type="hidden" name="token" value="'.$token.'"/>
		
		'.Controls::input (array (
			label=>'Hasło *<em>(6-20 znaków)', 
			name=>'password', 
			type=>'password', 
			required=>'required', 
			max=>'20', 
			classes=>'short')).'
		'.Controls::input (array (
			label=>'Powtórz hasło *', 
			name=>'retype_password', 
			type=>'password', 
			required=>'required', 
			max=>'20', 
			classes=>'short')).'
		<p class="important">W przypadku zablokowanego konta, zostanie ono odblokowane.</p>

        <div class="submit"><input type="submit" value="zmień hasło" name="submit"/> lub <a href="'.url (true).'#tab-users!" class="cancel">anuluj</a></div>
        </fieldset>
        </form>';

    }

	function add ($data) {
		allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);

		if ($data['password']['value'] === $data['retype_password']['value']) {
	        $result = Db::query ('insert into user(login, password, email,fail, date) values("'.$data['login']['value'].'","'.md5 ($data['password']['value'].PREFIX).'","'.$data['email']['value'].'",0,NOW())');
		} else {
			$result = 'Hasła się różnią, popraw i zapisz ponownie.';
		}

        redirect ($result, $_POST['redirect'], 'Użytkownik "'.$data['login']['value'].'" został dodany.');
    }
	
	function update ($data, $id) {
		allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);

        $result = Db::query ('update user set login="'.$data['login']['value'].'", fail=0, email="'.$data['email']['value'].'", date=NOW() where id='.$id);

        redirect ($result, $_POST['redirect'], 'Zmiany użytkownika "'.$data['login']['value'].'" zostały zapisane.');
	}

	function setPassword ($data, $id) {
		allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);

        $user = Users::getUser ($id);

		if ($data['password']['value'] === $data['retype_password']['value']) {
        	$result = Db::query ('update user set password="'.md5 ($data['password']['value'].PREFIX).'", fail=0, date=NOW() where id='.$id);
		} else {
			$result = 'Hasła się różnią, popraw i zapisz ponownie.';
		}
		
        redirect ($result, $_POST['redirect'], 'Hasło użytkownika "'.$user[0]['login'].'" zostało zmienione.');
	}
	
	function remove ($id) {
		allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);
        
		$user = Users::getUser ($id);
        
        $result = Db::query ('delete from user where id='.$id);

        redirect ($result, $_POST['redirect'], 'Użytkownik "'.$user[0]['login'].'" został usunięty.');
	}
		
}; // end class
	
?>