<?php  
if ( count( get_included_files() ) == 1) die( '--access denied--' );

define('___MATRICULE___','HE201330');

if(stripos($_SERVER['PHP_SELF'],___MATRICULE___)==FALSE) {
	trigger_error("TENTATIVE DE FRAUDE de {$_SERVER['PHP_SELF']} chez ".___MATRICULE___, E_USER_ERROR);
	exit;
} 
else{
	$__INFOS__ = array(   'matricule'=> ___MATRICULE___
					,'host' => 'localhost'
					,'user' => 'BRANCART'
					,'pswd' => 'Clementx2P6'
					,'dbName' => '1718he201330'
					,'nom' => 'BRANCART'
					,'prenom' => 'Clement'  
					,'classe' => '2TM1'  
					);
}
