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
// Obtengo la informacion de ubicacion del cliente        //
// Fecha de creacion 20/04/2018                           //
// Autor: Pablo Maroli                                    //
////////////////////////////////////////////////////////////
$app->get('/getProfessionalLocation', function (Request $request, Response $response) {
    
        $userID = $request->getAttribute('userID'); //userID obtenido desde el Middleware

        try {
                //Creo una nueva conexión
                $conn = Database::getInstance()->getDb();
                $consulta = "call pfl_getProfessionalLocation(:userID);";
                $comando = $conn->prepare($consulta);
                $comando->bindValue(':userID', $userID);
                $comando->execute();
                $values = $comando->fetch(PDO::FETCH_ASSOC);     

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
                $respuesta["status"] = array("code" => 502, "description" => requestStatus(502));       
        } 
        catch (Exception $e) 
        {
                $respuesta["status"] = array("code" => 501, "description" => requestStatus(501));       
        }      

    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

})->add($AuthUserPermisson);


////////////////////////////////////////////////////////////
// Inserta o actualiza la ubicacion del cliente           //
// Fecha de creacion 20/04/2018                           //
// Autor: Pablo Maroli                                    //
////////////////////////////////////////////////////////////
$app->post('/insertProfessionalLocation', function (Request $request, Response $response) {


    //Obtengo y limpio las variables
    $userID = $request->getAttribute('userID'); //userID obtenido desde el Middleware
        
    $countryID = 12; //Argentina

    $stateProvinceID = $request->getParam('stateProvinceID');
    $stateProvinceID = clean_var($stateProvinceID);

    $cityID = $request->getParam('cityID');
    $cityID = clean_var($cityID);

    $streetAddress = $request->getParam('streetAddress');
    $streetAddress = clean_var($streetAddress);

    $lat = 0;
    $lng = 0;

        //Obtengo la longitud y latitud invocando al servicio de consulta de google
        try {
            $consulta = "call cty_getCityByID(:cityID);";
            //Creo una nueva conexión
            $conn = Database::getInstance()->getDb();
            //Preparo la consulta
            $comando = $conn->prepare($consulta);
            //bindeo el parámetro a la consulta
            $comando->bindValue(':cityID', $cityID);
            // Ejecutar sentencia preparada
            $comando->execute();
            //Obtengo el arreglo de registros
            $values = $comando->fetch(PDO::FETCH_ASSOC);
            $cityName = $values["cityName"];

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
            $values = $comando->fetch(PDO::FETCH_ASSOC);
            $stateProvinceName = $values["stateProvinceName"];


            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            ); 

            $key = 'AIzaSyDHZWk6wvrIU69JDY1-KN-WoSAvXKg2Phw';

            $address1 = $streetAddress.", ".$cityName.", ".$stateProvinceName.", Argentina";

            $url1 = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address1)."&amp;sensor=false&ampkey=".$key;

            $url1_data = file_get_contents($url1, false, stream_context_create($arrContextOptions));

            $json1_data = json_decode($url1_data, true);
      

            if ($json1_data['status'] == 'OK') {
                
                $lat = $json1_data['results'][0]['geometry']['location']['lat'];
                $lng = $json1_data['results'][0]['geometry']['location']['lng'];
            }
            else
                $respuesta["status"] = array("code" => 909, "description" => requestStatus(909));  
        }  
        catch (Exception $e) 
        {
                $respuesta["status"] = array("code" => 501, "description" => requestStatus(501));       
        } 
// Fin obtener distancia


if ($userID != '')
{
    try {

        // Preparar sentencia
        $consulta = "call pfl_insProfessionalLocation(:userID, :countryID, :stateProvinceID, :cityID, :streetAddress, :lat, :lng);";

        //Creo una nueva conexión
        $conn = Database::getInstance()->getDb();
        //Preparo la consulta
        $comando = $conn->prepare($consulta);
        //bindeo el parámetro a la consulta
        $comando->bindValue(':userID', $userID);
        $comando->bindValue(':countryID', $countryID);
        $comando->bindValue(':stateProvinceID', $stateProvinceID);
        $comando->bindValue(':cityID', $cityID);
        $comando->bindValue(':streetAddress', $streetAddress);
        $comando->bindValue(':lat', $lat);
        $comando->bindValue(':lng', $lng);

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


//Ejecución de la sentencia del FW NO BORRAR
$app->run();
?>