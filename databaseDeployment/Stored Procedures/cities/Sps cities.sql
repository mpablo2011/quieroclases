-- Devuelve el listado de ciudades
DELIMITER $$
DROP PROCEDURE IF EXISTS cty_getCities $$
CREATE PROCEDURE cty_getCities()

BEGIN

SELECT cityID, cityName
FROM cities;


END $$
DELIMITER;

-- Dado un ID devuelve la descripción de la ciudad
DELIMITER $$
DROP PROCEDURE IF EXISTS cty_getCityByID $$
CREATE PROCEDURE cty_getCityByID(IN _cityID int)

BEGIN

SELECT cityID, cityName
FROM cities
WHERE cityID = _cityID;


END $$
DELIMITER;

-- Devuelve el listado de ciudades asociadas a una provincia
DELIMITER $$
DROP PROCEDURE IF EXISTS cty_getCitiesByStateProvinceID $$
CREATE PROCEDURE cty_getCitiesByStateProvinceID(IN _stateProvinceID int)

BEGIN

SELECT stateProvinceID, cityID, cityName
FROM cities
WHERE stateProvinceID = _StateProvinceID;


END $$


-- Inserta una nueva ciudad
DELIMITER $$
DROP PROCEDURE IF EXISTS cty_insertCity $$
CREATE PROCEDURE cty_insertCity(IN _stateProvinceID int, IN _cityName int)
BEGIN

INSERT INTO cities
(stateProvinceID, cityName)
VALUES
(_stateProvinceID, _cityName);

END $$

-- Elimina una ciudad 
DELIMITER $$
DROP PROCEDURE IF EXISTS cty_deleteCity $$
CREATE PROCEDURE cty_deleteCity(IN _cityID int)
BEGIN

delete from cities where cityID = _cityID;

END $$
