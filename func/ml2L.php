<?php
if (!defined('pvault_panel')){ die('Not Found');}

function ml2L($ml, $s=2){
	
	if($ml > 1000){
		$conv = number_format($ml/1000, $s) .'L';
	}else{
		$conv = number_format($ml, $s).'ml';
	}
	return $conv;
	
}

?>