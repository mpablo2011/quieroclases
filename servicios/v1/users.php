<?php
//Defino la cabecera y el tipo de encoding
header('content-type: application/json; charset=utf-8');

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//incluyo el framework
require '../../vendor/autoload.php';

//incluyo la conexion a db y las funciones
require '../../database/database.php';
require '../../utiles/funciones.php';
require '../../security/auth.php';
require '../../middlewares/middleware.php';
require_once '../../utiles/variables.php';
//require 'mail.php';


//Variables de debug
$GLOBALS["debugMode"] = true; //Si está en false enmascara el error

$app = new \Slim\App();


////////////////////////////////////////////////////////////
// Inserta o actualiza informacion basica del usuario     //
// Fecha de creacion 20/04/2018                           //
// Autor: Pablo Maroli                                    //
////////////////////////////////////////////////////////////
$app->post('/insertuserinformation', function (Request $request, Response $response) {


    //Obtengo y limpio las variables
    $userID = $request->getAttribute('userID'); //userID obtenido desde el Middleware
        
    $firstName = $request->getParam('firstName');
    $firstName = clean_var($firstName);

    $lastName = $request->getParam('lastName');
    $lastName = clean_var($lastName);

    $birthdate = $request->getParam('birthdate');
    $birthdate = fecha_sql(clean_var($birthdate));

    $sexID = $request->getParam('sexID');
    $sexID = clean_var($sexID);

    $areaCode = $request->getParam('areaCode');
    $areaCode = clean_var($areaCode);

    $phoneNumber = $request->getParam('phoneNumber');
    $phoneNumber = clean_var($phoneNumber);


if ($userID != '')
{
    try {

        // Preparar sentencia
        $consulta = "call usi_insUserInformation(:userID, :firstName, :lastName, :birthdate, :sexID, :areaCode, :phoneNumber);";

        //Creo una nueva conexión
        $conn = Database::getInstance()->getDb();
        //Preparo la consulta
        $comando = $conn->prepare($consulta);
        //bindeo el parámetro a la consulta
        $comando->bindValue(':userID', $userID);
        $comando->bindValue(':firstName', $firstName);
        $comando->bindValue(':lastName', $lastName);
        $comando->bindValue(':birthdate', $birthdate);
        $comando->bindValue(':sexID', $sexID);
        $comando->bindValue(':areaCode', $areaCode);
        $comando->bindValue(':phoneNumber', $phoneNumber);

        // Ejecutar sentencia preparada
        $comando->execute();

        //Armo la respuesta
        $respuesta["status"] = array("code" => 200, "description" => requestStatus(200)); //OK


        //Elimino la conexión
        $comando  = null;
        $conn = null;
    }
    catch (PDOException $e)
    {
        if($GLOBALS["debugMode"] == true)
            $respuesta["status"] = array("errmsg" => $e->getMessage());
        else
            $respuesta["status"] = array("code" => 502, "description" => requestStatus(502));
    }
    catch (Exception $e)
    {
        if($GLOBALS["debugMode"] == true)
            $respuesta["status"] = array("errmsg" => $e->getMessage());
        else
            $respuesta["status"] = array("code" => 501, "description" => requestStatus(501));
    }
}
else
{
    $respuesta["status"] = array("code" => 907, "description" => requestStatus(907));
}

    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

})->add($AuthUserPermisson);



////////////////////////////////////////////////////////////
// Crea un nuevo usuario                                  //
// Fecha de creacion 20/04/2018                           //
// Autor: Pablo Maroli                                    //
////////////////////////////////////////////////////////////
$app->post('/insertuser', function (Request $request, Response $response) {

//Obtengo y limpio las variables
$userEmail = $request->getParam('userEmail');
$userEmail = clean_var($userEmail);

$userPassword = $request->getParam('userPassword');
$userPassword = clean_var($userPassword);

$roleID = $request->getParam('roleID');
$roleID = clean_var($roleID);

//Valido que el rol no este vacio y que no sea administrador
if ($roleID == ''|| $roleID == ADMIN_ROLE_ID)
{
    $respuesta["status"] = array("code" => 910, "description" => requestStatus(910));
}
else
{
    //Valido que el mail y la contraseña no esten vacias
    if ($userEmail == '' || $userPassword == '')
    {
        $respuesta["status"] = array("code" => 906, "description" => requestStatus(906));
    }
    else
    {
        //Valido si el mail tiene un formato correcto
        if (isValidEmail($userEmail))
        {
            try 
            {

                // Preparar sentencia
                $consulta = "call usr_insUser(:userEmail);";

                //Creo una nueva conexión
                $conn = Database::getInstance()->getDb();
                //Preparo la consulta
                $comando = $conn->prepare($consulta);
                //bindeo el parámetro a la consulta
                $comando->bindValue(':userEmail', $userEmail);

                // Ejecutar sentencia preparada
                $comando->execute();
                //Obtengo el arreglo de registros
                $userData = $comando->fetch(PDO::FETCH_ASSOC);

                if( $userData["userID"] == -1) {
                    //El mail ingresado ya existe
                    $respuesta["status"] = array("code" => 905, "description" => requestStatus(905)); //OK
                }
                else {
                    try
                    {
                        // Inserto la contraseña
                        $consulta = "call pwd_insPassword(:userID, :userPassword);";
                        $conn = Database::getInstance()->getDb();
                        $comando = $conn->prepare($consulta);
                        $comando->bindValue(':userID', $userData["userID"]);
                        $comando->bindValue(':userPassword', $userPassword);
                        $comando->execute();
                        try
                        {
                            // Inserto el rol
                            $consulta = "call url_insertUserRole(:userID, :roleID);";
                            $conn = Database::getInstance()->getDb();
                            $comando = $conn->prepare($consulta);
                            $comando->bindValue(':userID', $userData["userID"]);
                            $comando->bindValue(':roleID', $roleID);
                            $comando->execute();

                            //inserto una nueva instancia de profesional o cliente
                            if($roleID == CLIENT_ROLE_ID)
                            {
                                $consulta = "call cli_insertClient(:userID);";
                                $conn = Database::getInstance()->getDb();
                                $comando = $conn->prepare($consulta);
                                $comando->bindValue(':userID', $userData["userID"]);
                                $comando->execute();
                            }
                            
                            if($roleID == PROFESSIONAL_ROLE_ID)
                            {
                                $consulta = "call pfl_insertProfessional(:userID);";
                                $conn = Database::getInstance()->getDb();
                                $comando = $conn->prepare($consulta);
                                $comando->bindValue(':userID', $userData["userID"]);
                                $comando->execute();
                            }

                            // DESHABILITADO EL ENVIO DE MAIL POR EL MOMENTO
                            /*
                            //Si todo salio bien dejo envío un mail con el token de validacion
                            $consulta = "call ntf_insertNotification(:userID, :notificationType);";
                            $conn = Database::getInstance()->getDb();
                            $comando = $conn->prepare($consulta);
                            $comando->bindValue(':userID', $userData["userID"]);
                            $comando->bindValue(':notificationType', AUTH_MAIL_NOTIFICATION);
                            $comando->execute();
                            */
                            $respuesta["status"] = array("code" => 200, "description" => requestStatus(200)); //OK


                        }
                        catch (PDOException $e) 
                        {
                            // Si no pude insertar el rol, elimino el usuario y password
                            $consulta = "call usr_deleteUser(:userID);";
                            $conn = Database::getInstance()->getDb();
                            $comando = $conn->prepare($consulta);
                            $comando->bindValue(':userID', $userData["userID"]);
                            $comando->execute();

                            $consulta = "call usr_deleteUserPassword(:userID);";
                            $conn = Database::getInstance()->getDb();
                            $comando = $conn->prepare($consulta);
                            $comando->bindValue(':userID', $userData["userID"]);
                            $comando->execute();

                            $respuesta["status"] = array("code" => 501, "description" => requestStatus(501));       
                        } 
                        catch (Exception $e) 
                        {
                            $respuesta["status"] = array("code" => 501, "description" => requestStatus(501));       
                        }
                    }
                    catch (PDOException $e) 
                    {
                        // Si algo no funciono elimino el usuario que acabo de crear
                        $consulta = "call usr_deleteUser(:userID);";
                        $conn = Database::getInstance()->getDb();
                        $comando = $conn->prepare($consulta);
                        $comando->bindValue(':userID', $userData["userID"]);
                        $comando->execute();

                        $respuesta["status"] = array("code" => 501, "description" => requestStatus(501));       
                    } 
                    catch (Exception $e) 
                    {
                        $respuesta["status"] = array("code" => 501, "description" => requestStatus(501));       
                    }

                    //Si todo sale bien, armo la respuesta
                    $respuesta["status"] = array("code" => 200, "description" => requestStatus(200)); //OK

                }
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
}

    //Elimino la conexión
    $comando  = null;
    $conn = null;

    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

});


////////////////////////////////////////////////////////////
// Obtengo la informacion de usuario                      //
// Fecha de creacion 20/04/2018                           //
// Autor: Pablo Maroli                                    //
////////////////////////////////////////////////////////////
$app->get('/getUserInformation', function (Request $request, Response $response) {

//Obtengo y limpio las variables
$userID = $request->getAttribute('userID'); //userID obtenido desde el Middleware

if ($userID != '')
{
    try {

        // Preparar sentencia
        $consulta = "call usi_getUserInformation(:userID);";
        //Creo una nueva conexión
        $conn = Database::getInstance()->getDb();
        //Preparo la consulta
        $comando = $conn->prepare($consulta);
        $comando->bindValue(':userID', $userID);
        // Ejecutar sentencia preparada
        $comando->execute();
        //Obtengo el arreglo de registros
        $values = $comando->fetchAll(PDO::FETCH_ASSOC);

        //Armo la respuesta
        if($values)
        {
            $respuesta["status"] = array("code" => 200, "description" => requestStatus(200)); //OK
            $respuesta["values"] = $values;
        }
        else
        {
            $respuesta["status"] = array("code" => 502, "description" => requestStatus(502)); // No data found
        }

        //Elimino la conexión
        $comando  = null;
        $conn = null;
    }
    catch (PDOException $e)
    {
        if(DEBUG_MODE == true)
            $respuesta["status"] = array("errmsg" => $e->getMessage());
        else
            $respuesta["status"] = array("code" => 502, "description" => requestStatus(502));
    }
    catch (Exception $e)
    {
        if(DEBUG_MODE == true)
            $respuesta["status"] = array("errmsg" => $e->getMessage());
        else
            $respuesta["status"] = array("code" => 501, "description" => requestStatus(501));
    }
}    
else
{
    $respuesta["status"] = array("code" => 907, "description" => requestStatus(907));
}

//Realizo el envío del mensaje
return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

})->add($AuthUserPermisson);


// Verifico el token de la aplicación que invoca el servicio
//$app->add($AuthAppKey);

//Ejecuto el FrameWork
$app->run();
?>