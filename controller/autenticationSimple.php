<?php
require_once '../vendor/autoload.php';
require_once '../security/auth.php';
require_once '../database/database.php';
require_once '../utiles/funciones.php';

if(!isset($_GET['mail']) || !isset($_GET['pwd'])) die('usuario o password no especificados');

//Obtengo y limpio las variables
$userEmail = $_GET['mail'];
$userEmail = clean_var($userEmail);

$userPassword = $_GET['pwd'];
$userPassword = clean_var($userPassword);


    echo Auth::SignIn([
        'userID' => 999,
        'userName' => $userEmail,
        'userPassword' => $userPassword
    ]);

?>