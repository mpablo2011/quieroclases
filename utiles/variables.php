<?php
/**
 * Provee las constantes para conectarse a la base de datos
 * Mysql.
 */
define("APP_KEY", "TEST");// Key de la aplicación solicitante
define("ADMIN_NAME", "ADMIN");// Nombre del usuario admin
define("GUEST_USER_ROLE", "2");// Role del usuario Guest
define("TEST_MODE", false); // Si el modo de prueba se encuentra activado, entonces no se realizará la atutenticación de usuario.
define("IP_RETRY", "20");
define("USER_AUTH_PENDING_ID", "3");
define("DEBUG_MODE", "true");
define("SERVICES_ROUTE", "http://localhost/quieroservicios/servicios/v1");
define("AUTH_MAIL_NOTIFICATION", "1");

//CONFIGURACION DE ROLES
define("ADMIN_ROLE_ID", "1");
define("CLIENT_ROLE_ID", "3");
define("PROFESSIONAL_ROLE_ID", "4");


//Mail configuration vars
define("MAIL_SMTPDEBUG", "0");// Key de la aplicación solicitante
define("MAIL_MAILER", "smtp");// Key de la aplicación solicitante
define("MAIL_HOST", "smtp.gmail.com");// Key de la aplicación solicitante
define("MAIL_PORT", "587");// Key de la aplicación solicitante
define("MAIL_SMTPSECURE", "tls");// Key de la aplicación solicitante
define("MAIL_SMTPAUTH", "true");// Key de la aplicación solicitante
define("MAIL_USERNAME", "quieroserviciosargentina@gmail.com");// Key de la aplicación solicitante
define("MAIL_PASSWORD", "biohazard2236");// Key de la aplicación solicitante

?>