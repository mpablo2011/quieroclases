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


//Variables de debug
$GLOBALS["debugMode"] = false; //Si está en false enmascara el error

//Unicializo el framework
$app = new \Slim\App();

// obtengo todos los proyectos de un usuario
$app->get('/getProjectsByUserID', function (Request $request, Response $response) {

    //Obtengo y limpio las variables
    $userID = $request->getAttribute('userID'); //userID obtenido desde el Middleware

    try {
        // Preparar sentencia
        $consulta = "call prj_getProjectsByUserID(:userID);";
        //Creo una nueva conexión
        $conn = Database::getInstance()->getDb();
        //Preparo la consulta
        $comando = $conn->prepare($consulta);
        //bindeo el parámetro a la consulta
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
            $respuesta["status"] = array("code" => 911, "description" => requestStatus(911)); // No data found
        }

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

    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

})->add($AuthUserPermisson);


// obtengo la información de un proyecto enviando por parametro un id de proyecto
$app->get('/getProjectByProjectID/{projectID}', function (Request $request, Response $response) {

    // Preparar sentencia
    $consulta = "call ;";

    //Obtengo y limpio las variables
    $userName = $request->getAttribute('userName');
    $userName = clean_var($userName);

    try {
        $conn = Database::getInstance()->getDb();
        //Preparo la consulta
        $comando = $conn->prepare($consulta);
        //bindeo el parámetro a la consulta
        $comando->bindValue(':userName', $userName);
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

    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

});


// Inserto un nuevo proyecto
$app->post('/insertProject', function (Request $request, Response $response) {


    //Obtengo y limpio las variables
    $userID = $request->getAttribute('userID'); //userID obtenido desde el Middleware

    $projectName = $request->getParam('projectName');
    $projectName = clean_var($projectName);

    $professionID = $request->getParam('professionID');
    $professionID = clean_var($professionID);

    $projectDescription = $request->getParam('projectDescription');
    $projectDescription = clean_var($projectDescription);

if ($userID != '' && $professionID != '' && $projectName != '')
{
    try {

        // Preparar sentencia
        $consulta = "call prj_insProjectByUserID(:userID, :projectName, :professionID, :projectDescription);";

        //Creo una nueva conexión
        $conn = Database::getInstance()->getDb();
        //Preparo la consulta
        $comando = $conn->prepare($consulta);
        //bindeo el parámetro a la consulta
        $comando->bindValue(':userID', $userID);
        $comando->bindValue(':projectName', $projectName);
        $comando->bindValue(':professionID', $professionID);
        $comando->bindValue(':projectDescription', $projectDescription);

        // Ejecutar sentencia preparada
        $comando->execute();

        //Armo la respuesta
        if($comando->rowCount() == 1)
        {
            $respuesta["status"] = array("code" => 200, "description" => requestStatus(200)); //OK
        }
        else
        {
            $respuesta["status"] = array("code" => 408, "description" => requestStatus(408));
        }

        //Elimino la conexión
        $comando  = null;
        $conn = null;
    }
    catch (PDOException $e)
    {
        if($GLOBALS["debugMode"] == true)
            $respuesta["status"] = array("errmsg" => $e->getMessage());
        else
        {
            switch ($comando->errorCode()) {
                case '23000':
                    $respuesta["status"] = array("code" => 23001, "description" => requestStatus(23001));
                    break;
                
                default:
                   $respuesta["status"] = array("code" => 501, "description" => requestStatus(501));
                    break;
            };
            
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
    $respuesta["status"] = array("code" => 907, "description" => requestStatus(907));
}

    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

})->add($AuthUserPermisson);

// Elimino un proyecto por su id
$app->delete('/deleteproject', function (Request $request, Response $response) {
    
    //Obtengo y limpio las variables
    $userID = $request->getAttribute('userID'); //userID obtenido desde el Middleware
    $projectID = $request->getParam('projectID');
    $projectID = clean_var($projectID);

    if ($userID != '' && $projectID != '')
    {
        try {
                // Preparar sentencia
                $consulta = "call prj_deleteProject(:userID, :projectID);";

                //Creo una nueva conexión
                $conn = Database::getInstance()->getDb();
                //Preparo la consulta
                $comando = $conn->prepare($consulta);
                //bindeo el parámetro a la consulta
                $comando->bindValue(':userID', $userID);
                $comando->bindValue(':projectID', $projectID);
                // Ejecutar sentencia preparada
                $comando->execute();
                //Obtengo el arreglo de registros

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

// obtengo todos los proyectos de un usuario
$app->get('/getProjectsByProfessionalID', function (Request $request, Response $response) {

    //Obtengo y limpio las variables
    $userID = $request->getAttribute('userID'); //userID obtenido desde el Middleware

    try {
        // Preparar sentencia
        $consulta = "call prj_getProjectsByProfessionalID(:userID);";
        //Creo una nueva conexión
        $conn = Database::getInstance()->getDb();
        //Preparo la consulta
        $comando = $conn->prepare($consulta);
        //bindeo el parámetro a la consulta
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
            $respuesta["status"] = array("code" => 911, "description" => requestStatus(911)); // No data found
        }

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

    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

})->add($AuthUserPermisson);


// Verifico el token de la aplicación que invoca el servicio
//$app->add($AuthAppKey);

//Ejecuto el FrameWork
$app->run();
?>