<?php
/**
 * Clase que envuelve una instancia de la clase PDO
 * para el manejo de la base de datos
 */

//require_once '../database/mysql_login.php';
require_once 'mysql_login.php';


class Database
{

    /**
     * Única instancia de la clase
     */
    private static $db = null;

    /**
     * Instancia de PDO
     */
    private static $pdo;

    final private function __construct()
    {
        try {
            // Crear nueva conexión PDO
            self::getDb();
        } catch (PDOException $e) {
            echo 'Error al obtener la base de datos: ' . $e->getMessage();
        }


    }

    /**
     * Retorna en la única instancia de la clase
     * @return Database|null
     */
    public static function getInstance()
    {
        if (self::$db === null) {
            self::$db = new self();
        }
        return self::$db;
    }

    /**
     * Crear una nueva conexión PDO basada
     * en los datos de conexión
     * @return PDO Objeto PDO
     */
    public function getDb()
    {
            if (self::$pdo == null) {
                try
                {
                    self::$pdo = new PDO(
                        'mysql:dbname=' . DATABASE .
                        ';host=' . HOSTNAME .
                        ';port:63343;',
                        USERNAME,
                        PASSWORD,
                        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
                    );
                }
                catch (PDOException $e) 
                {
                     echo 'Falló la conexión: ' . $e->getMessage();
                }

                // Habilitar excepciones
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }

            return self::$pdo;

    }

    /**
     * Evita la clonación del objeto
     */
    final protected function __clone()
    {
    }

    function _destructor()
    {
        self::$pdo = null;
    }
}

?>