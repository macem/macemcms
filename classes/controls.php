<?php

/**
 * class Controls
 * @author macem
 * @package macemCMS
*/

class Controls extends Db 
{
	function tabByLang ($langs, $values, $obj, $prefix, $column, $control = null) {
		
		//$langs = Lang::get();
		$names = '';
		$index = 0;
		$tabs = array();
		//$table = explode (',', $page[0]['name']);
		
		foreach ($langs as $key => $item) {	
			
			$names .= Controls::tabPane ('tab_'.$prefix.$item[$column], ($index==0?true:false));
			$names .= Controls::input (array(label=>$obj['label'], name=>$prefix.$item[$column], id=>$prefix.$item[$column], value=>Db::html ($values[$index]) ));
			$names .= Controls::tabPaneEnd();
			$index++;
			
			$tabs['tab_'.$prefix.$item[$column]] = $item[$column];
		}
		
		return array (html=>$names, tabs=>$tabs);		
	}
	
	function textarea ($obj) {
		$label = '<label for="'.($obj['id']).'">'.($obj['label']).'</label>';
		$html = '<div class="'.($obj['parentClass']).'">'.$label.'<textarea name="'.($obj['name']).'" placeholder="'.$obj['placeholder'].'" rows="'.($obj['rows']).'" id="'.($obj['id']).'" class="'.($obj['classes']).'">';
		$html .= Db::html ($obj['value']).'</textarea></div>';
		return $html;
	}
	
	function input ($obj) {
		$label = '<label for="'._def($obj['id'],$obj['name']).'">'.($obj['label']).'</label>';
		$html = '<div class="'.($obj['parentClass']).'">'.$label.'<input type="'._def($obj['type'], 'text').'" '._def($obj['speech'], '').' '._def($obj['focus'], '').' '._def($obj['required'], '').' name="'.$obj['name'].'" placeholder="'.$obj['placeholder'].'" id="'._def($obj['id'],$obj['name']).'" maxlength="'._def($obj['max'], '255').'" class="'.$obj['classes'].'" value="'.Db::html ($obj['value']).'"/>';
		$html .= '</div>';
		return $html;
	}
	
	function checkbox ($obj) {
		$label = '<label for="'._def($obj['id'], $obj['name']).'_yes">'.$obj['label'].'</label>';
		$html = '<div class="checkbox">'.$label.'<input type="radio" '._def($obj['required'], '').' name="'.$obj['name'].'" id="'._def($obj['id'], $obj['name']).'_yes" class="'.$obj['classes'].'" value="true" '._if ($obj['value']=='true', '', 'checked="checked"').'/>';
		$html .= '<label for="'._def($obj['id'], $obj['name']).'_yes">Yes</label>';
		$html .= '<input type="radio" '._def($obj['required'], '').' name="'.$obj['name'].'" id="'._def($obj['id'], $obj['name']).'_no" class="'.$obj['classes'].'" value="false" '._if ($obj['value']=='false', '', 'checked="checked"').'/>';
		$html .= '<label for="'._def($obj['id'], $obj['name']).'_no">No</label>';
		$html .= '</div>';
		return $html;
	}	
	
	function hidden ($obj) {
		$html = '<input type="hidden" name="'.$obj['name'].'" value="'.Db::html ($obj['value']).'"/>';
		return $html;
	}			
	
	function submit ($obj) {
		// TODO languages
		$html = '<div class="'._def($obj['classes'],'submit').'"><input type="submit" value="'.($obj['value']).'" name="submit"/> lub <a href="'._def($obj['url'],'#anuluj').'" class="cancel">'._def($obj['cancel'],'cancel').'</a></div>';
		return $html;
	}
	
	function select ($array, $obj) {
		$label = '<label for="'.($obj['id']).'">'.($obj['label']).'</label>';
		
		$html = $label.'<select class="select" name="'.$obj['name'].'">';
		$index = 0;
		
		foreach ($array as $key => $item) {
			$html .= '<option'.($obj['value']==$item ? ' selected="selected"' : '').' value="'.$item.'">'.$item.'</option>';
		}	
		return $html.'</select>';
	}	

	function tabs ($array, $active=0, $arg) {
		$html = '<ul class="tab-control '.$arg['classes'].'">';
		$index = 0;
		
		foreach ($array as $key => $item) {
			$html .= '<li'.($index==$active ? ' class="tab-show"' : '').'><a href="#'.$key.'" title="'._def ($arg['title'],'tab').'">'.$item.'</a></li>';
			$index++;
		}	
		return $html.'</ul>';
	}
	
	function tabPane ($id, $show=false) {	
		return '<div id="'.$id.'" class="tab-pane'.($show==true ? ' tab-show' : '').'">';
	}
	function tabPaneEnd () {
		return '</div>';
	}
}	