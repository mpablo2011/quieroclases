-- Obtengo el listado de roles

DELIMITER $$
DROP PROCEDURE IF EXISTS rle_getRoles $$
CREATE PROCEDURE rle_getRoles()
BEGIN

SELECT roleID, roleName
FROM roles;

END $$
DELIMITER;


-- Obtengo un rol en base a su ID

DELIMITER $$
DROP PROCEDURE IF EXISTS rle_getRoleByID $$
CREATE PROCEDURE rle_getRoleByID(IN _roleID int)
BEGIN

SELECT roleID, roleName
FROM roles
WHERE roleID = _roleID;

END $$
DELIMITER;


-- Inserto un rol
DELIMITER $$
DROP PROCEDURE IF EXISTS rle_insertRole  $$
CREATE PROCEDURE rle_insertRole(IN _roleName varchar(50))
BEGIN

INSERT INTO roles
(roleName)
VALUES
(_roleName);

END $$
DELIMITER;



-- Actualizo un rol
DELIMITER $$
DROP PROCEDURE IF EXISTS rle_updateRole $$
CREATE PROCEDURE rle_updateRole(IN _roleID int, IN _roleName varchar(50))
BEGIN

update roles set
roleName = _roleName
where roleID = _roleID;

END $$
DELIMITER;


-- Elimino un rol
DELIMITER $$
DROP PROCEDURE IF EXISTS rle_deleteRole $$
CREATE PROCEDURE rle_deleteRole(IN _roleID int)
BEGIN

delete from roles where roleID = _roleID;

END $$
