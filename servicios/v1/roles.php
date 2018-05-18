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

// obtengo todos los roles
$app->get('/getroles', function (Request $request, Response $response) {

        try {
                // Preparar sentencia
                $consulta = "call rle_getRoles();";
                
            	//Creo una nueva conexión
                $conn = Database::getInstance()->getDb();
                //Preparo la consulta
                $comando = $conn->prepare($consulta);
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


// obtengo la informacion de un rol en base a su id
$app->get('/getrolebyid/{roleID}', function (Request $request, Response $response) {
    
     	// Preparar sentencia
		$consulta = "call rle_getRoleByID(:roleID);";

		//Obtengo y limpio las variables
        $roleID = $request->getAttribute('roleID');
        $roleID = clean_var($roleID);

        try {
                $conn = Database::getInstance()->getDb();
                //Preparo la consulta
                $comando = $conn->prepare($consulta);
                //bindeo el parámetro a la consulta
                $comando->bindValue(':roleID', $roleID);
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

// Inserto un nuevo rol
$app->post('/insertrole', function (Request $request, Response $response) {
    
        // Preparar sentencia
        $consulta = "call rle_insertRole(:roleName);";
        
        //Obtengo y limpio las variables
        $roleName = $request->getParam('roleName');
        $roleName = clean_var($roleName);

        try {                
                //Creo una nueva conexión
                $conn = Database::getInstance()->getDb();
                //Preparo la consulta
                $comando = $conn->prepare($consulta);
                //bindeo el parámetro a la consulta
                $comando->bindValue(':roleName', $roleName);
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

    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

});

// Actualizo un rol
$app->put('/updaterole', function (Request $request, Response $response) {
    
        // Preparar sentencia
        $consulta = "call rle_updateRole(:professionID, :professionName);";

        //Obtengo y limpio las variables
        $professionName = $request->getParam('professionName');
        $professionName = clean_var($professionName);
        $professionID = $request->getParam('professionID');
        $professionID = clean_var($professionID);

        try {
                //Creo una nueva conexión
                $conn = Database::getInstance()->getDb();
                //Preparo la consulta
                $comando = $conn->prepare($consulta);
                //bindeo el parámetro a la consulta
                $comando->bindValue(':professionName', $professionName);
                $comando->bindValue(':professionID', $professionID);
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

        //Realizo el envío del mensaje
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);

});

// Elimino un rol por su ID
$app->delete('/deleterole', function (Request $request, Response $response) {
    
        // Preparar sentencia
        $consulta = "call rle_deleteRole(:professionID);";

        //Obtengo y limpio las variables
        $professionID = $request->getParam('professionID');
        $professionID = clean_var($professionID);

        try {
                //Creo una nueva conexión
                $conn = Database::getInstance()->getDb();
                //Preparo la consulta
                $comando = $conn->prepare($consulta);
                //bindeo el parámetro a la consulta
                $comando->bindValue(':professionID', $professionID);
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

    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

});


// obtengo la informacion de un rol en base a su id
$app->get('/getrolesByUserID', function (Request $request, Response $response) {
    
        $userID = $request->getAttribute('userID'); //userID obtenido desde el Middleware

        try {
                //Creo una nueva conexión
                $conn = Database::getInstance()->getDb();
                $consulta = "call url_getUserRolesByUserID(:userID);";
                $comando = $conn->prepare($consulta);
                $comando->bindValue(':userID', $userID);
                $comando->execute();
                $values = $comando->fetchALL(PDO::FETCH_ASSOC);      

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

//Ejecución de la sentencia del FW NO BORRAR
$app->run();
?>