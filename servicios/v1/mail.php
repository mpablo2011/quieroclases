<?php
//Defino la cabecera y el tipo de encoding
header('content-type: application/json; charset=utf-8');

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//incluyo el framework
require '../../vendor/autoload.php';

//incluyo la conexion a db y las funciones
require '../../database/database.php';
require '../../utiles/funciones.php';
require '../../security/auth.php';
require '../../middlewares/middleware.php';
require_once '../../utiles/variables.php';




//Inicializo el framework
$app = new \Slim\App();

// Genero y envio un tocken de autenticacion
$app->get('/sendAuthMail', function (Request $request, Response $response) {

$complete = true;

try {
        // Preparar sentencia
        $consulta = "call ntf_getPendingNotifications(:notificationType);";

        $conn = Database::getInstance()->getDb();
        //Preparo la consulta
        $comando = $conn->prepare($consulta);
        //Bindeo los parametros de la consulta
        $comando->bindValue(':notificationType', AUTH_MAIL_NOTIFICATION);
        // Ejecutar sentencia preparada
        $comando->execute();
        //Obtengo el arreglo de registros
        $values = $comando->fetchAll(PDO::FETCH_ASSOC);

        foreach ($values as $key => $userData) 
        {
            //Verifico si existen datos para el usuario ingresado
            if($userData)
            {
                //Valido que el usuario se encuentre pendiente de autenticacion
                if($userData["userStatusID"] == USER_AUTH_PENDING_ID)
                {
                    $authToken = generarPassword();


                    // Preparar sentencia
                    $consulta = "call uat_insertUserAuthToken(:userID, :authentTokenValue);";

                    $conn = Database::getInstance()->getDb();
                    //Preparo la consulta
                    $comando = $conn->prepare($consulta);
                    //bindeo el parámetro a la consulta
                    $comando->bindValue(':userID', $userData["userID"]);
                    $comando->bindValue(':authentTokenValue', $authToken);
                    // Ejecutar sentencia preparada
                    $stmt = $comando->execute();

                    //Genero una nueva instancia de mail
                    $mail = new PHPMailer;

                    //Configuraciones
                    $mail->isSMTP();
                    //Habilitar SMTPOptions unicamente si el host modifica el certificado SSL
                    $mail->SMTPOptions = array(
                                'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                                )
                    );
                    $mail->SMTPDebug = MAIL_SMTPDEBUG;
                    $mail->Mailer = MAIL_MAILER;
                    $mail->Host = MAIL_HOST;
                    $mail->Port = MAIL_PORT;
                    $mail->SMTPSecure = MAIL_SMTPSECURE;
                    $mail->SMTPAuth = MAIL_SMTPAUTH;
                    $mail->Username = MAIL_USERNAME;
                    $mail->Password = MAIL_PASSWORD;
                    $mail->IsHTML(true);
                    $mail->SMTPDebug = 0;


                    $mail->setFrom(MAIL_USERNAME, 'QuieroServicios');
                    $mail->addAddress('mpablo2011@gmail.com', 'John Doe');
                    
                    $mail->Subject = 'Gracias por registrarse';
                    $mail->Body = "<p>autenticacion: <a href='".SERVICES_ROUTE."/authentication.php/authenticateUser/".$authToken."'  target='_blank'>Click</a></p>";

                    try
                    {
                        $mailsend = $mail->send();
                    }
                    catch (Exception $e) 
                    {
                        $complete = false;
                    }
                    


                    if ($mailsend && $stmt) 
                    { 
                        // Preparar sentencia
                        $consulta = "call ntf_updateNotificationStatus(:userID, :notificationType, :notificationStatus);";

                        $conn = Database::getInstance()->getDb();
                        //Preparo la consulta
                        $comando = $conn->prepare($consulta);
                        //bindeo el parámetro a la consulta
                        $comando->bindValue(':userID', $userData["userID"]);
                        $comando->bindValue(':notificationType', AUTH_MAIL_NOTIFICATION);
                        $comando->bindValue(':notificationStatus', 2);
                        // Ejecutar sentencia preparada
                        $stmt = $comando->execute();

                    } else 
                    {
                        $complete = false;
                    }
                }

            }
        }
    }
    catch (PDOException $e) 
    {
        if(DEBUG_MODE == true)
            $respuesta["status"] = array("errmsg5" => $e->getMessage());
        else
            $respuesta["status"] = array("code" => 502, "description" => requestStatus(502));       
    } 
    catch (Exception $e) 
    {
        if(DEBUG_MODE == true)
            $respuesta["status"] = array("errmsg6" => $e->getMessage());
        else
            $respuesta["status"] = array("code" => 501, "description" => requestStatus(501));       
    }

    if($complete = true)
        $respuesta["status"] = array("code" => 200, "description" => requestStatus(913));
    else
        $respuesta["status"] = array("code" => 914, "description" => requestStatus(914));


    //Elimino la conexión
    $comando  = null;
    $conn = null;   

    //Realizo el envío del mensaje
    return $response->withJson($respuesta,200, JSON_UNESCAPED_UNICODE);

});


//Ejecuto el FrameWork
$app->run();
?>