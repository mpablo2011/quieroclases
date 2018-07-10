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
$GLOBALS["debugMode"] = true; //Si está en false enmascara el error


//Inicializo el framework
$app = new \Slim\App();


// obtengo todas las profesiones
$app->post('/getProfessionalScores', function (Request $request, Response $response) {
    
    // Preparar sentencia
   $consulta = "call pfs_getProfessionalScores(:professionalID);";

    $professionalID = $request->getParam('professionalID');
    $professionalID = clean_var($professionalID);

   try {
          //Creo una nueva conexión
           $conn = Database::getInstance()->getDb();
           //Preparo la consulta
           $comando = $conn->prepare($consulta);
           //bindeo el parámetro a la consulta
           $comando->bindValue(':professionalID', $professionalID);

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

})->add($AuthUserPermisson);


// Inserto un nuevo proyecto
$app->post('/insertProfessionalScore', function (Request $request, Response $response) {

    $professionalID = $request->getParam('professionalID');
    $professionalID = clean_var($professionalID);

    $scoreID = $request->getParam('scoreID');
    $scoreID = clean_var($scoreID);

    $projectID = $request->getParam('projectID');
    $projectID = clean_var($projectID);

    $comments = $request->getParam('comments');
    $comments = clean_var($comments);

if ($professionalID != '' && $scoreID != '')
{
    try {

        // Preparar sentencia
        $consulta = "call pfs_insProfessionalScore(:professionalID, :scoreID, :projectID :comments);";

        //Creo una nueva conexión
        $conn = Database::getInstance()->getDb();
        //Preparo la consulta
        $comando = $conn->prepare($consulta);
        //bindeo el parámetro a la consulta
        $comando->bindValue(':professionalID', $professionalID);
        $comando->bindValue(':scoreID', $scoreII);
        $comando->bindValue(':projectID', $projectID);
        $comando->bindValue(':comments', $comments);

        // Ejecutar sentencia preparada
        $comando->execute();

        //Armo la respuesta
        if($comando->rowCount() == 1)
        {
            $respuesta["status"] = array("code" => 200, "description" => requestStatus(200)); //OK
        }
        else
        {
            $respuesta["status"] = array("code" => 410, "description" => requestStatus(410));
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

// obtengo todos los proyectos de un usuario
$app->post('/getProfessionalPendingScores', function (Request $request, Response $response) {

    $professionalID = $request->getParam('professionalID');
    $professionalID = clean_var($professionalID);

    try {
        // Preparar sentencia
        $consulta = "call pfs_getProfessionalPendingScores(:professionalID);";
        //Creo una nueva conexión
        $conn = Database::getInstance()->getDb();
        //Preparo la consulta
        $comando = $conn->prepare($consulta);
        //bindeo el parámetro a la consulta
        $comando->bindValue(':clientID', $clientID);

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
            $respuesta["status"] = array("code" => 911, "description" => requestStatus(918)); // No data found
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


// Verifico el token de la aplicación que invoca el servicio
//$app->add($AuthAppKey);

//Ejecuto el FrameWork
$app->run();
?>