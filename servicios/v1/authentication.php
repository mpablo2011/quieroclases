<?php
//Defino la cabecera y el tipo de encoding
header('content-type: application/json; charset=utf-8');

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//incluyo el framework
require '../../vendor/autoload.php';

//incluyo la conexion a db y las funciones
require_once '../../security/auth.php';
require_once '../../database/database.php';
require_once '../../utiles/funciones.php';
require_once '../../utiles/variables.php';

//Variables de debug
$GLOBALS["debugMode"] = true; //Si está en false enmascara el error

//Inicializo el framework
$app = new \Slim\App();

$app->post('/authenticateUser', function (Request $request, Response $response) {

//Obtengo y limpio las variables
$userEmail = $request->getParam('userEmail');
$userEmail = clean_var($userEmail);

$userPassword = $request->getParam('userPassword');
$userPassword = clean_var($userPassword);

    //Valido que el mail y la contraseña no esten vacias
    if ($userEmail == '' || $userPassword == '')
    {
        $respuesta["status"] = array("code" => 906, "description" => requestStatus(906));
    }
    else
    {
        //Valido si el mail tiene un formato correcto o si es el nombre del administrador
        if (isValidEmail($userEmail) || $userEmail == ADMIN_NAME)
        {
            try {

                    // Preparar sentencia
                    $consulta = "call usr_getUser(:userEmail);";

                    $conn = Database::getInstance()->getDb();
                    //Preparo la consulta
                    $comando = $conn->prepare($consulta);
                    //bindeo el parámetro a la consulta
                    $comando->bindValue(':userEmail', $userEmail);
                    // Ejecutar sentencia preparada
                    $comando->execute();
                    //Obtengo el arreglo de registros
                    $userData = $comando->fetch(PDO::FETCH_ASSOC);


                    //Verifico si existen datos para el usuario ingresado
                    if($userData)
                    {
                        if($userPassword == $userData["userPassword"])
                        {
                        	//Valido que el usuario se encuentre autenticado
                        	if($userData["isAuthenticated"] == 1)
                        	{
                        		switch ($userData["userStatusID"]) {
                        			// Usuario Activo
                                    case '1':
                        					//Armo La respuesta
                				        	//////////////////////////////Bloque de respuesta/////////////////////////////
                				        	try {
                						        	// Obtengo los roles del usuario
                						        	$consulta = "call url_getUserRolesByUserID(:userID);";
                						        	$comando = $conn->prepare($consulta);
                						        	$comando->bindValue(':userID', $userData["userID"]);
                						        	$comando->execute();
                									$values = $comando->fetchALL(PDO::FETCH_ASSOC);        	

                						        	//Si el usuario validó correctamente entonces genero el token
                						        	if($values)
                						        	{    
                							            $token = Auth::SignIn([
                							        			'userID' => $userData["userID"],
                							        			'userEmail' => $userEmail,
                							        			'userRoles' => $values

                							    		]);

                								        $respuesta["status"] = array("code" => 200, "description" => requestStatus(200)); //OK
                							            $respuesta["token"] = $token;
                							        }
                                                    else 
                                                    {
                                                        $respuesta["status"] = array("code" => 407, "description" => requestStatus(407));
                                                    }
                							    }
                							catch (PDOException $e) 
                							    {
                							        if($GLOBALS["debugMode"] == true)
                							            $respuesta["status"] = array("errmsg1" => $e->getMessage());
                							        else
                							            $respuesta["status"] = array("code" => 502, "description" => requestStatus(502));       
                							    } 
                							catch (Exception $e) 
                							    {
                							    	if($GLOBALS["debugMode"] == true)
                							            $respuesta["status"] = array("errmsg2" => $e->getMessage());
                							        else
                							            $respuesta["status"] = array("code" => 501, "description" => requestStatus(501));       
                							    }
                							//////////////////////////////Bloque de respuesta/////////////////////////////   
                        			break;
                                    //Usuario Inactivo
                        			case '2':
                        					$respuesta["status"] = array("code" => 902, "description" => requestStatus(902)); 
                        			break;
                                    // Usuario Pendiente
                        			case '3':
                        					$respuesta["status"] = array("code" => 903, "description" => requestStatus(903)); 
                        			break;
                                    // Usuario Bloqueado
                                    case '4':
                                            $respuesta["status"] = array("code" => 904, "description" => requestStatus(904)); 
                                    break;
                        			default:
                        				$respuesta["status"] = array("code" => 501, "description" => requestStatus(501));
                        			break;
                        		}
                        	}
                        	else 
                        	{
                        		$respuesta["status"] = array("code" => 903, "description" => requestStatus(903)); 
                        	}
                        }
                        else
                        {
                            //Incremento el failCount asociado al usuario
                            try {
                                // Preparar sentencia
                                $consulta = "call usr_increaseFailCountByUserID(:userID);";

                                $conn = Database::getInstance()->getDb();
                                //Preparo la consulta
                                $comando = $conn->prepare($consulta);
                                //bindeo el parámetro a la consulta
                                $comando->bindValue(':userID', $userData["userID"]);
                                // Ejecutar sentencia preparada
                                $comando->execute();

                                //Invalid Password
                                $respuesta["status"] = array("code" => 900, "description" => requestStatus(900));

                            }
                            catch (PDOException $e) 
                            {
                                if($GLOBALS["debugMode"] == true)
                                    $respuesta["status"] = array("errmsg3" => $e->getMessage());
                                else
                                    $respuesta["status"] = array("code" => 502, "description" => requestStatus(502));       
                            } 
                            catch (Exception $e) 
                            {
                                if($GLOBALS["debugMode"] == true)
                                    $respuesta["status"] = array("errmsg4" => $e->getMessage());
                                else
                                    $respuesta["status"] = array("code" => 501, "description" => requestStatus(501));       
                            }
                        }   
                    }
                    else
                    {
                        $respuesta["status"] = array("code" => 503, "description" => requestStatus(503)); 
                    }

                    //Elimino la conexión
                    $comando  = null;
                    $conn = null;
            	}
                catch (PDOException $e) 
                {
                    if($GLOBALS["debugMode"] == true)
                        $respuesta["status"] = array("errmsg5" => $e->getMessage());
                    else
                        $respuesta["status"] = array("code" => 502, "description" => requestStatus(502));       
                } 
                catch (Exception $e) 
                {
                    if($GLOBALS["debugMode"] == true)
                        $respuesta["status"] = array("errmsg6" => $e->getMessage());
                    else
                        $respuesta["status"] = array("code" => 501, "description" => requestStatus(501));       
                }      
        }
        else {
            $respuesta["status"] = array("code" => 908, "description" => requestStatus(908));
        }
    }

    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);
});

// Genero y envio un tocken de autenticacion
$app->get('/ValidateUserAuthToken/{token}', function (Request $request, Response $response) {

    //Obtengo y limpio las variables
    $token = $request->getAttribute('token');
    $token = clean_var($token);


    $respuesta["status"] = array("code" => 200, "description" => requestStatus(200)); //OK
    $respuesta["token"] = $token;

   ///Valido que el mail y la contraseña no esten vacias
    if ($token == '')
    {
        $respuesta["status"] = array("code" => 915, "description" => requestStatus(915));
    }
    else
    {
        $respuesta["status"] = array("code" => 200, "description" => requestStatus(916));
    }

    //Elimino la conexión
    $comando  = null;
    $conn = null;   

    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

});

$app->post('/requestGuestToken', function (Request $request, Response $response) {

    $token = Auth::SignIn([
            'userID' => '',
            'userEmail' => '',
            //Rol de invitado
            'userRoles' => GUEST_USER_ROLE

    ]);

    $respuesta["status"] = array("code" => 200, "description" => requestStatus(200)); //OK
    $respuesta["token"] = $token;

    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

});

////////////////////////////////////////////////////////////
// Autentico un usuario en base a su token                //
// Fecha de creacion 20/04/2018                           //
// Autor: Pablo Maroli                                    //
////////////////////////////////////////////////////////////
$app->get('/authenticateUser/{authentTokenValue}', function (Request $request, Response $response) {

//Obtengo y limpio las variables
$authentTokenValue = $request->getAttribute('authentTokenValue');
$authentTokenValue = clean_var($authentTokenValue);

//Valido que el rol no este vacio y que no sea administrador
if ($authentTokenValue == '')
{
    $respuesta["status"] = array("code" => 24001, "description" => requestStatus(24001));
}
else
{

    try {

        // Preparar sentencia
        $consulta = "call usr_authenticateUser(:authentTokenValue);";

        //Creo una nueva conexión
        $conn = Database::getInstance()->getDb();
        //Preparo la consulta
        $comando = $conn->prepare($consulta);
        //bindeo el parámetro a la consulta
        $comando->bindValue(':authentTokenValue', $authentTokenValue);

        // Ejecutar sentencia preparada
        $comando->execute();

        $respuesta["status"] = array("code" => 200, "description" => requestStatus(24002)); //OK

    }
    catch (PDOException $e)
    {
        $respuesta["status"] = array("code" => 24001, "description" => requestStatus(24001));
    }
    catch (Exception $e)
    {
        $respuesta["status"] = array("code" => 24001, "description" => requestStatus(24001));
    }
}

    //Elimino la conexión
    $comando  = null;
    $conn = null;

    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

});

//Ejecución de la sentencia del FW NO BORRAR
$app->run();
?>