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



// obtengo una profesión por id
$app->get('/getBudgetByBudgetID/{budgetID}', function (Request $request, Response $response) {

        try {
                // Preparar sentencia
                $consulta = "call rle_getRoleByID(:budgetID);";

                //Obtengo y limpio las variables
                $budgetID = $request->getAttribute('budgetID');
                $budgetID = clean_var($budgetID);

                $conn = Database::getInstance()->getDb();
                //Preparo la consulta
                $comando = $conn->prepare($consulta);
                //bindeo el parámetro a la consulta
                $comando->bindValue(':budgetID', $budgetID);
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

// Inserta un nuevo presupuesto
$app->post('/insertBudget', function (Request $request, Response $response) {

    // Preparar sentencia
    $consulta = "call bgt_insBudget(:userEmail);";

    //Obtengo y limpio las variables

    $userEmail = $request->getParam('userEmail');
    $userEmail = clean_var($userEmail);

    $userPassword = $request->getParam('userPassword');
    $userPassword = clean_var($userPassword);



    if ($userEmail != '' && $userPassword != '')
    {

        try {
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
                // Inserto la contraseña
                $consulta = "call pwd_insPassword(:userID, :userPassword);";
                $conn = Database::getInstance()->getDb();
                $comando = $conn->prepare($consulta);
                $comando->bindValue(':userID', $userData["userID"]);
                $comando->bindValue(':userPassword', $userPassword);
                $comando->execute();
                //Armo la respuesta
                $respuesta["status"] = array("code" => 200, "description" => requestStatus(200)); //OK
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
        $respuesta["status"] = array("code" => 906, "description" => requestStatus(906));
    }

    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

})->add($AuthUserPermisson);



// Verifico el token de la aplicación que invoca el servicio
$app->add($AuthAppKey);

//Ejecuto el FrameWork
$app->run();
?>