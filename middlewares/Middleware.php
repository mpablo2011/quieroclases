<?php

header('content-type: application/json; charset=utf-8');

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Middelware de autenticación de roles de usuario.
$AuthUserPermisson = function (Request $request, Response $response, $next) {

//se incluye un testmode de forma tal de saltear la autenticación en caso de que así se requiera
if(TEST_MODE == false)
{
    //Verifico que exista el token en la cabecera, caso contrario muestro un error.
    if(!empty($request->getHeader('Authorization')))
    {
        $headerValueArray = $request->getHeader('Authorization');
        $token = $headerValueArray[0];

        // Obtengo la información del token
        try
        {
          $userID = Auth::GetData($token)-> userID ;
          $userEmail = Auth::GetData($token)-> userEmail ;
          $userRoles = Auth::GetData($token)-> userRoles ;

          //Guardo la información del toquen para enviarla en el request
          $request = $request->withAttribute('userID', $userID);
          $request = $request->withAttribute('userEmail', $userEmail);
          $request = $request->withAttribute('userRoles', $userRoles);
        }
        catch (Exception $e)
        {
            $respuesta["status"] = array("code" => 498, "description" => requestStatus(498));
            return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);
        }


        //Obtengo los parámetros de la ruta
        if (!empty($request->getAttribute('route'))) 
        {
            $route = $request->getAttribute('route');
            $ruta = $route->getPattern();
            $ruta = substr($ruta, 1);

        }
        else
        {
            $respuesta["status"] = array("code" => 497, "description" => requestStatus(497));
            return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

        }

        //Obtengo los roles que poseen permisos para la ejecución dentro de la ruta
        try
        {
            $consulta = "call rlg_getRoleByObject(:ruta);";
            $conn = Database::getInstance()->getDb();
            $comando = $conn->prepare($consulta);
            $comando->bindValue(':ruta', $ruta);
            $comando->execute();

            $values = $comando->fetchAll(PDO::FETCH_ASSOC);

            //Elimino la conexión
            $comando  = null;
            $conn = null;
            //Variable de verificacion de permisos
            $auth = false;

            foreach ($values as $key => $value) 
            {
                for ($i = 0; $i < count($userRoles); $i++) 
                {
                    if($userRoles[$i] -> roleID == $value['roleID'])
                        $auth = true;
                }

            }

            // Si no pasó la autenticación salgo por mensaje de error
            if($auth == false)
            {
                $respuesta["status"] = array("code" => 406, "description" => requestStatus(406));
                return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

            }
            else
            {
                // Validaciones satisfactorias, puedo continuar el ciclo de ejecución.
                $response = $next($request, $response);
            }


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
        $respuesta["status"] = array("code" => 498, "description" => requestStatus(498));
        return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);
    }
}
else
{
	//Si el test_mode está activo realizo la ejecución sin realizar validaciones
	$response = $next($request, $response);
}
    //Retorno final de control NO ELIMINAR
    return $response;
};


//Validación de la clave secreta de la aplicación
$AuthAppKey = function (Request $request, Response $response, $next) {

//se incluye un testmode de forma tal de saltear la autenticación en caso de que así se requiera
if(TEST_MODE == false)
{
    if(!empty($request->getHeader('APPKEY')))
        {
        $headerValueArray = $request->getHeader('APPKEY');
        $headerValue = $headerValueArray[0];

        if ($headerValue == APP_KEY)
            $response = $next($request, $response);
        else
        {
            $respuesta["status"] = array("code" => 496, "description" => requestStatus(496));
            return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);
        }
    }
    else
    {
        $respuesta["status"] = array("code" => 496, "description" => requestStatus(496));
        return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);
    }
}
else
{
	$response = $next($request, $response);
}

    return $response;
};