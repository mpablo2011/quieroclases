-- Devuelve el listado de profesionales que trabajan en una determinada zona
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_getProfessionalsByWorkplaces $$
CREATE PROCEDURE sp_getProfessionalsByWorkplaces(IN _cityID int, IN _professionID int)

BEGIN

SELECT pp.professionalID
FROM professionalWorkplaces pw, ProfessionalProfessions pp
WHERE pp.professionalID = pw.professionalID
AND   pw.cityID = _cityID
AND   pp.professionID = _professionID;


END $$
DELIMITER;

-- Inserta un nuevo profesional
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_setProfessional $$
CREATE PROCEDURE sp_setProfessional(IN _userID int)

BEGIN

INSERT INTO professionals 
(userID)
VALUES
(_userID);

END $$
DELIMITER;

-- Genera una nueva asociacion entre un profesional y un lugar de trabajo
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_setProfessionalWorkplace $$
CREATE PROCEDURE sp_setProfessionalWorkplace(IN _professionalID int, IN _cityID int)

BEGIN

INSERT INTO professionalWorkplaces
(userID, cityID)
VALUES
(_userID, _cityID);

END $$
DELIMITER;

-- Genera una nueva asociacion entre un profesional y una profesion
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_setProfessionalProfession $$
CREATE PROCEDURE sp_setProfessionalProfession(IN _professionalID int, IN _professionID int)

BEGIN

INSERT INTO professionalProfessions
(professionalID, professionID)
VALUES
(_professionalID, _professionID);

END $$
DELIMITER;

-- Dado un userID devuelve el professionalID
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_getProfessionalIDByUserID $$
CREATE PROCEDURE sp_getProfessionalIDByUserID(IN _userID int)

BEGIN

SELECT professionalID
FROM professionals
WHERE userID = _userID;


END $$
DELIMITER;

-- Inserto una nueva profesión
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_setProfession $$
CREATE PROCEDURE sp_setProfession(IN _professionName varchar(50))
BEGIN

INSERT INTO professions
(professionName)
VALUES
(_professionName);

END $$
DELIMITER;

-- Actualizo una profesión
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_updateProfession $$
CREATE PROCEDURE sp_updateProfession(IN _professionID int, IN _professionName varchar(50))
BEGIN

update professions set
professionName = _professionName
where professionID = _professionID;

END $$
DELIMITER;

-- Elimino una profesión
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_deleteProfession $$
CREATE PROCEDURE sp_deleteProfession(IN _professionID int)
BEGIN

delete from professions where professionID = _professionID;

END $$
