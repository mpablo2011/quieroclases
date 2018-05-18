-- Devuelve el listado de provincias
DELIMITER $$
DROP PROCEDURE IF EXISTS stp_getStateProvinces $$
CREATE PROCEDURE stp_getStateProvinces()

BEGIN

SELECT stateProvinceID, countryID, stateProvinceName
FROM stateprovinces;


END $$
DELIMITER;


-- Dado un ID devuelve la descripción de la provincia

DELIMITER $$
DROP PROCEDURE IF EXISTS stp_getStateProvinceByID $$
CREATE PROCEDURE stp_getStateProvinceByID(IN _stateProvinceID int)

BEGIN

SELECT stateProvinceID, stateProvinceName
FROM stateprovinces
WHERE stateProvinceID = _stateProvinceID;


END $$
