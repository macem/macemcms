<?php

/**
 * class Module Util
 * @author macem
 * @package macemCMS
*/

class Module extends Db
{

	/**
	 * method connect to database
	 * @param $db - database: [mysql]
	*/
	function action ($name, $data) {
         switch ($name) {

             case 'send':
				
				if ($_POST['answer'] != 7) {
					//header ("Location: ".message ($_POST['redirect'], 'Jedno z pól formularza jest niepoprawne, popraw formularz i wyślij ponownie.'));
				
				} else if ($_POST['answer'] == 7) {
					$to = $GLOBALS['config']['email'];
					$subject = _define('company_name')." : zapytanie" ;
					$headers = "Reply-To: ".$to;
					$body = "Od:".$_POST['user'].' '.$_POST['phone'].' Pytanie: '.$_POST['question'];
					
					if (mail ($to, $subject, $body, $headers)) {
						//header ("Location: ".message ($_POST['redirect'], 'Dziękujemy, Twoje pytanie zostało wysłane. W przeciągu 24h skontaktujemy się z Tobą lub odpowiemy na pytanie.'));	
					} else {
						//header ("Location: ".message ($_POST['redirect'], 'Przepraszamy ale nie można wysłać zapytania. Skontaktuj się telefonicznie 660-359-970 lub wyślij maila z zewnętrzego programu pocztowego.'));
					}
				}
                
				break;

			case 'formhelp':

				$token = allowMethod ('GET', 'Method Not Allowed', null, true);
				
		        return '<form action="'.url (true).'util.help" method="post">
		        <fieldset>
		        <legend>Wyślij zapytanie:</legend>
				<input type="hidden" name="redirect" value="'.url (true).'"/>
		        <div><label for="question">Pomoc *</label><textarea name="question" id="question" maxlength="500" rows="5"></textarea></div>
		        <div class="submit"><input type="submit" value="Wyślij"/></div>
		        </fieldset>
		        </form>';
								
				break;
								
             case 'help':
				
				$token = allowMethod ('POST', 'Method Not Allowed', null, true);
				
				$subject = _define('company_name')." : pomoc" ;
				$headers = "Reply-To: ".$GLOBALS['config']['email'];
				$body = $GLOBALS['config']['email'].' Pytanie: '.$_POST['question'];
				
				$result = mail (SUPPORT, $subject, $body, $headers);
				
				if ($result) {
					redirect ($result, $_POST['redirect'], 'Dziękujemy, Twoje pytanie zostało wysłane.');	
				} else {
					redirect ($result, $_POST['redirect'], 'Przepraszamy, ale nie można wysłać zapytania na adres '.SUPPORT);
				}
                
				break;				
				
			case 'formproperty':
				
				$token = allowMethod ('GET', 'Method Not Allowed', null, true);
				
				$langs = Lang::get();
				$property = '';
				$tabs = array();
				$index = 0;
				
				foreach ($langs as $key => $item) {
					$variable = variableGet (Db::escape ($_GET['variable']), $item['id']);
					
					$property .= Controls::tabPane ('tab_'.$variable['name'].'_'.$item['path'], ($index==$GLOBALS['config']['langid']?true:false));
					$property .= '<em class="custom">Modyfikowany : <strong>'.$variable['date'].'</strong></em>';
					$property .= Controls::textarea (array (label=>'Treść:', name=>$item['path'], id=>'property'.$_GET['variable'].$item['path'], rows=>'6', value=>$variable['value']));
					$property .= Controls::tabPaneEnd();
					$index++;
					
					$tabs['tab_'.$variable['name'].'_'.$item['path']] = strtoupper ($item['path']);
				}
				
				return '
				
				<form class="form-save-property ajax-replace ajax-sel#property-'.$_GET['variable'].' ajax-parent" action="'.url(true).'util.saveproperty" method="post">
				<fieldset>
				<legend>Zapisz text:</legend>
				<strong class="required">* - pole wymagane</strong>
				<input type="hidden" name="variable" value="'.$_GET['variable'].'"/>
				<input type="hidden" name="redirect" value="'.$_SERVER['HTTP_REFERER'].'"/>
				'.Controls::tabs ($tabs, $GLOBALS['config']['langid'], 'Wersja językowa').$property.'
				'.Controls::submit (array(classes=>'submit', value=>'Zapisz', cancel=>'anuluj')).'
				</fieldset>
				</form>';				
				break;
			
			case 'wysiwygproperty':
				
				$token = allowMethod ('GET', 'Method Not Allowed', null, true);
				
				$langs = Lang::get();
				$property = '';
				$tabs = array();
				$index = 0;
				
				foreach ($langs as $key => $item) {
					$variable = variableGet (Db::escape ($_GET['variable']), $item['id']);
					
					$property .= Controls::tabPane ('tab_'.$variable['name'].'_'.$item['path'], ($index==$GLOBALS['config']['langid']?true:false));
					$property .= '<em class="custom">Modyfikowany : <strong>'.$variable['date'].'</strong></em>';
					$property .= Controls::textarea (array(label=>'Treść:', name=>$item['path'], id=>'property'.$_GET['variable'].$item['path'], classes=>'editable', rows=>'16', value=>$variable['value']));
					$property .= Controls::tabPaneEnd();
					$index++;
					
					$tabs['tab_'.$variable['name'].'_'.$item['path']] = strtoupper ($item['path']);
				}
				
				return '
				
				<form class="form-save-property" action="'.url(true).'util.saveproperty" method="post">
				<fieldset>
				<legend>Zapisz text:</legend>
				<strong class="required">* - pole wymagane</strong>
				<input type="hidden" name="variable" value="'.$_GET['variable'].'"/>
				<input type="hidden" name="redirect" value="'.$_SERVER['HTTP_REFERER'].'"/>
				'.Controls::tabs ($tabs, $GLOBALS['config']['langid'], 'Wersja językowa').$property.'
				'.Controls::submit (array(classes=>'submit', value=>'Zapisz', cancel=>'anuluj')).'
				</fieldset>
				</form>';				
				
				break;                              	
                				
			case 'formdelete':
		
				$token = allowMethod ('GET', 'Method Not Allowed', null, true);
				
	            return '
				
				<form class="form-delete ajax-replace ajax-sel'.$_GET['replace'].'" action="'.url(true).$_GET ['action'].'" method="post">

				<h2>Czy chcesz usunąć <strong>'.$_GET['info'].'</strong></h2>
				<input type="hidden" name="redirect" value="'.url (true).'"/>
				<input type="hidden" name="item" value="'.$_GET['item'].'"/>
				<input type="hidden" name="token" value="'.$token.'"/>
				<div class="submit"><input type="submit" value="Usuń" name="submit"/> lub <a href="'.url (true).'" class="cancel">anuluj</a></div>

				</form>';

				break;
				
			case 'formdefine':

				$token = allowMethod ('GET', 'Method Not Allowed', null, true);
				
		        return '<form class="form-add-define ajax-replace ajax-sel#tab-defines" action="'.url (true).'util.adddefine" method="post">
		        <fieldset>
		        <legend>Dodaj zmienną:</legend>
				<strong class="required">* - pole wymagane</strong>
				<input type="hidden" name="redirect" value="'.url (true).'#tab-defines!"/>
				<input type="hidden" name="token" value="'.$token.'"/>				
		        <div><label for="id">Nazwa *<em>(6-20 znaków)</em></label><input type="text" id="id" name="id" class="short" maxlength="20"/></div>
				<div><label for="value">Wartość</label><input type="text" id="value" name="value" class="short"/></div>
        		<div class="submit"><input type="submit" value="Dodaj" name="submit"/> lub <a href="'.url (true).'#tab-defines!" class="cancel">anuluj</a></div>
		        </fieldset>
		        </form>';
								
				break;							

			case 'adddefine':
				
				allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);
				
				$data  = Db::valid();
				
				$result = Db::query ('insert into define(name, value) values("'.$data['id']['value'].'","'.$data['value']['value'].'")');
				
				redirect ($result, $_POST['redirect'], 'Zmienna '.$data['id']['value'].' została dodana');
				
				break;
			
			case 'savedefine':
				
				allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);
				
				$data  = Db::valid();
				
				$result = Db::query ('update define set value="'.$data['value']['value'].'" where name="'.$data['id']['value'].'"');
				
				redirect ($result, $_POST['redirect'], "Zmiany zostały zapisane");
				
				break;
				
			case 'deletedefine':
				
				allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);
		        
		        $result = Db::query ('delete from define where name="'.Db::escape ($_POST['item']).'"');
		
		        redirect ($result, $_POST['redirect'], 'Zmienna "'.Db::escape ($_POST['item']).'" została usunięta.');
				
				break;				
												
			case 'saveproperty':
				
				allowMethod ('POST', 'Method Not Allowed', $_POST['token'], true);
				
				$data  = Db::valid(); 
				$query = array();
				$langs = Lang::get();
				
				//print_r ($data);
				
				foreach ($langs as $key => $item) {
					$variable = variableGet ($data['variable']['value'], $item['id']);

					if (isset ($variable['value'])) {
						array_push ($query ,'UPDATE config SET value="'.cleanHTML ($data[$item['path']]['value']).'", date=NOW() WHERE lang='.$item['id'].' AND name="'.$data['variable']['value'].'"');
					} else {
						array_push ($query ,'INSERT INTO config(name, value, lang, date) VALUES("'.cleanHTML ($data['variable']['value']).'","'.$data[$item['path']]['value'].'",'.$item['id'].',NOW())');
					
					}	
				}
				
				// multiple query TODO
				foreach ($query as $item) {
					$result = Db::query ($item);
					//print_r ($result);
					if (!$result) redirect ($result, $_POST['redirect'], "Błąd zapisu ! ".$item);	
				}
				
				if ($result) redirect ($result, $_POST['redirect'], "Zmiany zostały zapisane");
				
				//$result = Db::query ( implode ($query, ' UNION ') );

				/*if ($result) {
					header ("Location: ".$_POST['redirect']."?info=Zmiany zostały zapisane");
				} else {
					header ("Location: ".$_POST['redirect']."?info=Błąd zapisu !");
				}*/	
									
				//$result = Db::query ();
				/*if (isset ($_POST['property'])) {
					$variable = variableGet (Db::escape ($_POST['variable']));
					
					if ($variable['value'] != null) {
						$result = Db::query ('UPDATE config SET value="'.Db::escape ($_POST['property']).'", date=NOW() WHERE lang='.$GLOBALS['config']['langid'].' && name="'.Db::escape ($_POST['variable']).'"');
					} else {
						$result = Db::query ('INSERT INTO config(name, value, lang, date) VALUES("'.Db::escape ($_POST['variable']).'","'.Db::escape ($_POST['property']).'",'.$GLOBALS['config']['langid'].',NOW())');
					}
					if ($result) {
						header ("Location: ".$_POST['redirect']."?info=Zmiany zostały zapisane");
					} else {
						header ("Location: ".$_POST['redirect']."?info=Błąd zapisu !");
					}	
				}*/		
				break;		

         }
	}

};
?>