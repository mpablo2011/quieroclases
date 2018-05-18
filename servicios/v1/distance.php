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



//Variables de debug
$GLOBALS["debugMode"] = true; //Si está en false enmascara el error


//Inicializo el framework
$app = new \Slim\App();

// obtengo todos los roles
$app->get('/getdistance', function (Request $request, Response $response) {

        try {


            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            ); 

            $key = 'AIzaSyDHZWk6wvrIU69JDY1-KN-WoSAvXKg2Phw';

            $address1 = "Fermin Rosell 685, Baradero, Buenos Aires, Argentina";
            $address2 = "Fermin Rosell 674, Baradero, Buenos Aires, Argentina";

            $url1 = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address1)."&amp;sensor=false&ampkey=".$key;
            $url2 = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address2)."&amp;sensor=false&ampkey=".$key;

            $url1_data = file_get_contents($url1, false, stream_context_create($arrContextOptions));
            $url2_data = file_get_contents($url2, false, stream_context_create($arrContextOptions));

            $json1_data = json_decode($url1_data, true);
            $json2_data = json_decode($url2_data, true);
      

            if ($json1_data['status'] == 'OK' && $json2_data['status'] == 'OK') {
                
                $longlat1 = $json1_data['results'][0]['geometry']['location']['lat'].",".$json1_data['results'][0]['geometry']['location']['lng'];
                $longlat2 = $json2_data['results'][0]['geometry']['location']['lat'].",".$json2_data['results'][0]['geometry']['location']['lng'];

                $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$longlat1."&destinations=".$longlat2."&sensor=false";
                $result = file_get_contents($url, false, stream_context_create($arrContextOptions));
                $json = json_decode($result, true);

                if ($json['status'] == 'OK') {
                    $respuesta = $json["rows"][0]['elements'][0]['distance']['text'];
                }
                else
                $respuesta["status"] = array("code" => 909, "description" => requestStatus(909));  

            }
            else
                $respuesta["status"] = array("code" => 909, "description" => requestStatus(909));  
            

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



//Ejecución de la sentencia del FW NO BORRAR
$app->run();
?>