-- Obtengo el listado de profesiones

DELIMITER $$
DROP PROCEDURE IF EXISTS pfn_getProfessions $$
CREATE PROCEDURE pfn_getProfessions()
BEGIN

SELECT professionID, professionName
FROM professions;

END $$
DELIMITER;


-- Obtengo una profesión en base a su ID

DELIMITER $$
DROP PROCEDURE IF EXISTS pfn_getProfessionByID $$
CREATE PROCEDURE pfn_getProfessionByID(IN _professionID int)
BEGIN

SELECT professionID, professionName
FROM professions
WHERE professionID = _professionID;

END $$
DELIMITER;


-- Inserto una profesión
DELIMITER $$
DROP PROCEDURE IF EXISTS pfn_insertProfession  $$
CREATE PROCEDURE pfn_insertProfession(IN _professionName varchar(50))
BEGIN

INSERT INTO professions
(professionName)
VALUES
(_professionName);

END $$
DELIMITER;



-- Actualizo una profesión

DELIMITER $$
DROP PROCEDURE IF EXISTS pfn_updateProfession $$
CREATE PROCEDURE pfn_updateProfession(IN _professionID int, IN _professionName varchar(50))
BEGIN

update professions set
professionName = _professionName
where professionID = _professionID;

END $$
DELIMITER;

-- Elimino una profesión

DELIMITER $$
DROP PROCEDURE IF EXISTS pfn_deleteProfession $$
CREATE PROCEDURE pfn_deleteProfession(IN _professionID int)
BEGIN

delete from professions where professionID = _professionID;

END $$
