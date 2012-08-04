<?php

class Field extends Controls 
{
        public $fields;
        
        function __construct() {
	 $this->fields = array (
	         'img' => function($name, $item, $field) {
		  return Field::imageBrowse ($name, $item, $field); },
		       
	         'title' => function($name, $item, $field) {
		  return Field::titleInput ($name, $item, $field); },
	         
	         'input' => function($name, $item, $field) {
		  return Field::input ($name, $item, $field); },
		       
	         'hidden' => function($name, $item, $field) {
		  return Field::hidden ($name, $item, $field); },			
	         
	         'checkbox' => function($name, $item, $field) {
		  return Field::checkboxInput ($name, $item, $field); },
		       
	         'content' => function($name, $item, $field) {
		  return Field::contentEditor ($name, $item, $field); },
	         
	         'description' => function($name, $item, $field) {
		  return Field::description ($name, $item, $field); }			           
	 );
	 
	 //return $this->fields;
        }
        
        function get() {
	 return $this->fields;
        }

        function setField ($key, $function) {
	 $this->fields[$key] = function($name, $item, $field) {
	          $function ($name, $item, $field);
	 };
        }
        
        function render ($from, $field_key, $value) {
	 $field = array(
	         'label'    => $from[$field_key][1],
	         'classes'  => $from[$field_key][2],
	         'size'     => $from[$field_key][3],
	         'parentClass' => $from[$field_key][4],
	         'value'    => $value
	 );
        
	 return $field;    
        }       
        
        function hidden ($name, $item, $field) {
	 $table = $GLOBALS['config']['table'];
	 
	 return Controls::hidden (array (label=>$field['label'], name=>$name, id=>$name, value=>_def ($field['value'],'') ));	
        }	

        function input ($name, $item, $field) {
	 $table = $GLOBALS['config']['table'];
	 
	 return Controls::input (array (label=>$field['label'], name=>$name, id=>$name, parentClass=>$field['parentClass'], classes=>$field['classes'], max=>$field['size'], value=>_def ($field['value'],'') ));	
        }
        
        function titleInput ($name, $item, $field) {
	 $table = $GLOBALS['config']['table'];
	 
	 return Controls::input (array (label=>$field['label'], name=>$name, id=>$name, parentClass=>$field['parentClass'], classes=>'sidy '.$field['classes'], max=>$field['size'], value=>_def ($field['value'],'') ));	
        }
        
        function checkboxInput ($name, $item, $field) {
	 $table = $GLOBALS['config']['table'];
	 //echo $field['value'];
	 return Controls::checkbox (array (label=>$field['label'], name=>$name, id=>$name, parentClass=>$field['parentClass'], classes=>'checkbox '.$field['classes'], value=>_def ($field['value'], 'false') ));	
        }		
        
        function contentEditor ($name, $item, $field) {
	 $table = $GLOBALS['config']['table'];
	 
	 return Controls::textarea (array (label=>$field['label'], name=>$name, id=>$name, parentClass=>$field['parentClass'], classes=>$field['classes'], rows=>'20', value=>_def ($field['value'],'') ));	
        }
        
        function description ($name, $item, $field) {
	 $table = $GLOBALS['config']['table'];
	 
	 return Controls::textarea (array (label=>$field['label'], name=>$name, id=>$name, parentClass=>$field['parentClass'], classes=>$field['classes'], rows=>'4', value=>_def ($field['value'],'') ));	
        }	
        
        function imageBrowse ($name, $item, $field) {
	 $table = $GLOBALS['config']['table'];
	 
	 return Controls::input (array (label=>$field['label'], name=>$name, id=>$name, parentClass=>$field['parentClass'], classes=>$field['classes'], value=>_def ($field['value'],'') ));	     
        }	

}; // end class

?>