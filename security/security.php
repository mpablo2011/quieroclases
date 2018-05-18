<?php
/* Inicializamos nuevamente la sesion*/
session_start();

/* Establecemos que las paginas no pueden ser cacheadas */
header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


// Verificamos que el usuario se encuentre logeado
if(!isset($_SESSION["SKey"]) or !isset($_COOKIE['SKey'])) 
{ 
	logOut();
	header("Location: login.php"); die(); 
}

//Obtengo la cantidad de segundos que pasaron desde que se inició la session
$segundos=strtotime('now') - strtotime($_SESSION['last_access']);
//Si paso mas de una hora, la sesion caduca.
if( !isset($_SESSION['last_access']) || $segundos > 3600 )
{ 
	logOut();
	header("Location: login.php"); die(); 
}

// Verificamos que el valor de la security key logueada en la cookie recibida desde el cliente coincida con el valor de session 
if($_SESSION["SKey"] <> $_COOKIE['SKey']) 
{ 
	logOut();
	header("Location: login.php"); die(); 
}
/*
//verifico que el usuario tenga el rol correspondiente
if (!$_SESSION['role'])
{ 
	logOut();
	header("Location: login.php"); die(); 
}

// Verificamos que el usuario tenga permiso para acceder a la sección que desea
if(isset ($_GET['cat'])) 
{ 
	//Solo el menú de administración es accesible para todos los usuarios.
	if($_GET['cat'] <> 'admin_menu')
	{
		// Obtengo los permosos asociados al rol del usuario
		$q=mysql_query( "SELECT 1
						 FROM   grants grt, role_grants rlg
						 WHERE  rlg.grantID = grt.grantID
						 AND    rlg.roleID =  ".$_SESSION['role']."
						 AND    grt.grantLink like '".$_GET['cat']."'
						 UNION
						 SELECT 1
						 FROM 	grants
						 WHERE	grantLink like '".$_GET['cat']."'
						 AND	menu_option = 2;",$conexion);
		//Si el role no tiene permiso de acceso, elimino la session.
		$cant = mysql_num_rows($q);
		if($cant == 0)
		{
			logOut();
			header("Location: no.php"); die(); 
		}
	}
}
*/
?>
