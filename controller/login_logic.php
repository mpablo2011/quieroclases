<?php
//incluyo la conexion a db
  include "conection.php";
//inicio la sesion
session_start();
//generador de tokens
$num = md5(microtime().rand());
global $num;

function new_id() {
	$prefijo = 'skey_';
    return uniqid($prefijo); 
};

//Generador de cadena para recuperación de contraseñas
# Genera un string de $tamanio caracteres
# tiene incluido tambien numeros
function generarPassword()
{
	$string = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$tamanio = 8;
	for($i=0;$i<$tamanio;$i++){
		$pos = rand(0,58);
		$str .= $string{$pos};
	}
	return $str;
};

?>
<?php
// Funcion para eliminar caracteres especiales dentro de los campos recibidos
function quitar($mensaje)
{
	$nopermitidos = array("'",'\\','<','>',"\"","--","<?","?>", "/");
	$mensaje = str_replace($nopermitidos, "", $mensaje);
	return $mensaje;
}     

//Si el usuario vuelve al menú de login, destruyo su sesion y lo traigo otra vez
if(isset($_SESSION["SKey"])) 
{ 
	//destruyo la sesion
	session_unset();
	session_destroy();
	//elimino la cookie asociada
	if (isset($_COOKIE['SKey']))
	setcookie("SKey","",time()-1000,"/");
	//redirecciono
	header("Location: login.php"); die(); }
?>
<?php
	/* 
		Below is a very simple example of how to process a login request.
		Some simple validation (ideally more is needed).
	*/

//Forms posted
if(!empty($_POST))
{
		$errores = "";
		// Verifico y obtengo las variables
		if (isset($_REQUEST["mail"]))
		$mail = quitar($_REQUEST["mail"]);
		else
		$mail = "";
		if (isset($_REQUEST["password"]))
		$password = quitar($_REQUEST["password"]);
		else
		$password = "";

		// Verifico que el usuario recibido exista y en ese caso, obtengo su contraseña
		$registro=mysql_query( "SELECT USR.PASSWORD PASSWORD, USR.ISACTIVE ISACTIVE, USR.ISBLOCKED ISBLOCKED, USR.FAILCOUNT FAILCOUNT, USR.ROLEID ROLE, USR.MAILSENDED MAILSENDED
								 FROM users USR
								 WHERE USR.MAIL = '".$mail."'
								 ",$conexion) or die("Problemas:".mysql_error());
		
		// obtengo el registro
		$reg=mysql_fetch_array($registro);
		
		//Verifico si existe el usuario con el mail ingresado	
		if(mysql_num_rows($registro)==1)
		{
			
			//Verifico que el usuario se encuentre activo 0) Pendiente 1) Aceptado 1) Rechazado.
			$isActive = $reg['ISACTIVE'];
			
			//Si el usuario fue activado, entonces comienzo a validar.
			if ($isActive == 1)
			{
				//Verifico que el usuario no se encuentre bloqueado.
				$isBlocked = $reg['ISBLOCKED'];
				//Si el usuario esta bloqueado entonces muestro un mensaje de error.
				if($isBlocked == 1)
				{
					$errores = "<div>
							  <h2>Se ha producido un error!</h2>
							  <span>El usuario ingresado se encuentra bloqueado.</span>
							  <br />
							  <span>Por favor pongase en contacto con un administrador.</span>
							  </div>";
					session_unset();
					session_destroy();
				}
				//Si el usuario no se encuentra bloqueado.
				else
				{
					// Almaceno la password recibida de la base de datos.
					$pwrd = utf8_encode($reg['PASSWORD']);
					//Comparo la password recibida con la password obtenida.
					if($password == $pwrd)
					{
						//Obtengo el rol del usuario
						$role = $reg['ROLE'];
						//Obtengo la fecha y la hora del sistema
						$mysqltime = date("Y-m-d H:i:s");
						//Limpio la cantidad de reintentos realizados por el usuario y actualizo la fecha del último acceso.
						$consulta=mysql_query( "UPDATE users
												SET FAILCOUNT = 0,
													MAILSENDED = 0,
													LASTLOGGIN = '".$mysqltime."'
												WHERE MAIL = '".$mail."'
									 ",$conexion) or die("Problemas:".mysql_error());
						//Genero una nueva sesion y almaceno los datos de la misma.
						$_SESSION['mail']= $mail;
						$_SESSION['last_access'] = $mysqltime;
						$_SESSION['role'] = $role;
						$_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
						$_SESSION['SKey'] = new_id();
						$_SESSION['LastActivity'] = $_SERVER['REQUEST_TIME'];
						//Genero la cookie del usuario utilizando el SKey que acabo de generar
						setcookie("SKey",$_SESSION['SKey'],time()+3600,"/");
						//Envio al usuario al menu correspondiente (Respetando su respectivo rol).
						if($role == 1)//admin
						{
							header("Location: index.php?cat=admin_menu");
						}
						if($role == 2)//Usuario general o invitado
						{
							header("Location: index.php?cat=admin_menu");
						}
						if($role == 3)//Usuario general o invitado
						{
							header("Location: index.php?cat=admin_menu");
						}
					}
					else
					{
						//Obtengo la cantidad de reintentos.
						$failCount = $reg['FAILCOUNT'];
						//Si la cantidad de reintentos es menor a 6 (5 reintentos) sumo uno al contador, caso contrario bloqueo el usuario.
						if($failCount<6)
						{
							$reintentos = 6 - $failCount;
							$failCount++;
							$consulta=mysql_query( "UPDATE users
													SET FAILCOUNT = ".$failCount."
													WHERE MAIL = '".$mail."'
									 ",$conexion) or die("Problemas:".mysql_error());
							$errores = utf8_encode("<div class='header_01'>Se ha producido un error!</div>
														 <span>La contrase&ntilde;a ingresada es incorrecta.
														 <br />
														 Ud. dispone de ".$reintentos." reintentos m&aacute;s.</span>
														 <br />");
							session_unset();
							session_destroy();
						}
						//Genero una nueva password y se la informo a través de un mail, si la password ya fúe informada, entonces bloqueo al usuario.
						else
						{
							$mailSended = $reg['MAILSENDED'];
							
							if($mailSended == 0)
							{
							//genero una nueva password
							$newPassword = generarPassword();
							// actualizo la información del usuario
							$consulta=mysql_query( "UPDATE users
													SET FAILCOUNT = 0,
														MAILSENDED = 1,
														PASSWORD = '".$newPassword."'
													WHERE MAIL = '".$mail."'
										 ",$conexion) or die("Problemas:".mysql_error());
							$errores ="<div class='header_01'>Se ha producido un error!</div>
									   <span>En instantes recibir&aacute; un correo electr&oacute;nico con su nueva contrase&ntilde;a.</span>
									   <br />
									   <span>Utilice la contrase&ntilde;a recibida para poder acceder.</span>";
							//Estructura de envio del mail con la nueva contraseña
							$destinatario = $mail;														
  							$asunto = "Su nueva Password"; 
							$cuerpo = " 
							<html> 
							<head> 
							   <title></title> 
							</head> 
							<body>  
							<p> 
							Estimado usuario: <br>
							Lamentamos informarle que por cuestiones de seguridad, su contrase&ntilde;a ha sido modificada. <br>
							A fin de poder acceder nuevamente al sistema, deber&aacute; utilizar la siguiente contrase&ntilde;a: ".$newPassword." <br>
							Lamentamos profundamente los inconvenientes generados. <br>
							</p> 
							</body> 
							</html> 
							"; 
							
							//para el envío en formato HTML 
							$headers = "MIME-Version: 1.0\r\n"; 
							$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
							
							//dirección del remitente 
							$headers .= "From: Robot <info@agendamev-pba.com>\r\n"; 
							
							mail($destinatario,$asunto,$cuerpo,$headers); 
							//Fin de estructura de envio
							}
							else //Bloqueo el usuario
							{
							// actualizo la información del usuario
							$consulta=mysql_query( "UPDATE users
													SET FAILCOUNT = 0,
														ISBLOCKED = 1
													WHERE MAIL = '".$mail."'
										 ",$conexion) or die("Problemas:".mysql_error());
							$errores ="<div class='header_01'>Se ha producido un error!</div>
									   <span>Lamentamos informarle que su usuario ha sido bloqueado.</span>
									   <br />
									   <span>Por fav&oacute;r contacte a un administrador.</span>";
							}
							
							session_unset();
							session_destroy();				
						}
					}
				}
			}
			else
			{
				//Si la solicitud recibida se encuentra pendiente de aprobacion.
				if ($isActive == 0)
				{
					$errores = utf8_encode("<div class='header_01'>Se ha producido un error!</div>
											<span>La solicitud de activación se encuentra pendiente de aprobación.</span>
											<br />
											<span>Por favor pongase en contacto con un administrador.</span>");
					session_unset();
					session_destroy();
				}
				//Si la solicitud no se encuentra pendiente, significa que la misma fue rechazada.
				else
				{
					$errores = utf8_encode("<div class='header_01'>Se ha producido un error!</div>
											<span>Su solicitud de activación ha sido rechazada por el administrador.</span>
											<br />");
					session_unset();
					session_destroy();
				}
			}		
		}
		//Si no lo encuentro muestro un mensaje de error
		else
		{
		$errores = utf8_encode("<div>
								<h2>Se ha producido un error!</h2>
								<br />
								<span>El mail o la contrase&ntilde;a ingresadas son incorrectas.</span>
								</div>");
		session_unset();
		session_destroy();
		}
}
?>