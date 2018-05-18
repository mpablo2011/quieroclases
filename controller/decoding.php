<?php

require_once '../vendor/autoload.php';
require_once '../security/auth.php';

if(!isset($_GET['token'])) die('Debe especificar el token');

$token = $_GET['token'];

try
{

	//Instancio el ID de usuario
	$userID = Auth::GetData($token)-> userID ;
	echo "ID del usuario instanciado = ";
	echo $userID;
	echo "<br>";
	//Instancio el mail de usuario
	$userEmail = Auth::GetData($token)-> userEmail ;
	echo "Mail del usuario instanciado = ";
	echo $userEmail;
	echo "<br>";
	//Instancio los roles del usuario
	$userRoles = Auth::GetData($token)-> userRoles ;
	echo "Roles del usuario instanciado = ";
	echo count($userRoles);
	echo "<br>";
	for ($i = 0; $i < count($userRoles); $i++) 
	{
    	echo "Rol: " .  $userRoles[$i] -> roleID;
    	echo "<br>";
	}
		
	// Muestro todas las variables
	echo "<br>Objeto Instanciado:";
	echo "<br>";
	var_dump(Auth::GetData($token));
}
catch (Exception $e)
{
        print get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine();
}
