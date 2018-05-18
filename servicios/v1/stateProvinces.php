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
// Obtengo el listado de ciudades                         //
// Fecha de creacion 20/04/2018                           //
// Autor: Pablo Maroli                                    //
////////////////////////////////////////////////////////////
$app->get('/getstateprovinces', function (Request $request, Response $response) {
    

        try {
                $countryID = 10; // Argentina.
                 // Preparar sentencia
                $consulta = "call stp_getStateProvinces(:countryID);";
            	//Creo una nueva conexión
                $conn = Database::getInstance()->getDb();
                //Preparo la consulta
                $comando = $conn->prepare($consulta);
                //bindeo el parámetro a la consulta
                $comando->bindValue(':countryID', $countryID);
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


////////////////////////////////////////////////////////////
// Obtengo una ciudad por su ID                           //
// Fecha de creacion 20/04/2018                           //
// Autor: Pablo Maroli                                    //
////////////////////////////////////////////////////////////
$app->get('/getstateprovincebyid/{stateProvinceID}', function (Request $request, Response $response) {
    
    //Obtengo y limpio las variables
    $stateProvinceID = $request->getAttribute('stateProvinceID');
    $stateProvinceID = clean_var($stateProvinceID);

    if ($stateProvinceID != '')
    {

        try {
                // Preparar sentencia
                $consulta = "call stp_getStateProvinceByID(:stateProvinceID);";
            	//Creo una nueva conexión
                $conn = Database::getInstance()->getDb();
                //Preparo la consulta
                $comando = $conn->prepare($consulta);
                //bindeo el parámetro a la consulta
                $comando->bindValue(':stateProvinceID', $stateProvinceID);
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
    }
    else {
        $respuesta["status"] = array("code" => 907, "description" => requestStatus(907));
    }


    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

});

//Ejecución de la sentencia del FW NO BORRAR
$app->run();
?>