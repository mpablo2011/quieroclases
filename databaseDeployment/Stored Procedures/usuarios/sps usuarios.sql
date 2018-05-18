-- Dado un ID, devuelve la información del usuario.
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_getUserInformation $$
CREATE PROCEDURE sp_getUserInformation(
  IN _userID int)

BEGIN

SELECT userID, 
       userName, 
       userStatusID, 
       docTypeID, 
       docNumber, 
       firstName, 
       lastName, 
       birthdate, 
       isAuthenticated, 
       authenticationDate, 
       streetAddress, 
       cityID, 
       stateProvinceID, 
       countryID, 
       zipCode, 
       areaCode, 
       phoneNumber, 
       emailAddress, 
       sexID, 
       lastUpdate, 
       lastLoggin, 
       registerDate, 
       failCount
FROM quieroservicios.users
WHERE userID = _userID;


END $$
DELIMITER;

-- Devuelve el listado de roles de usuario
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_getRoles $$
CREATE PROCEDURE sp_getRoles()

BEGIN

SELECT roleID, roleName, roleDescription
FROM roles;


END $$
DELIMITER;

-- Devuelve el listado de roles de un usuario
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_getUserRolesByUserID $$
CREATE PROCEDURE sp_getUserRolesByUserID(IN _userID int)

BEGIN

IF ((SELECT COUNT(*) FROM userRoles WHERE userID = _userID) >= 1)
THEN
SELECT userID, roleID
FROM userRoles
WHERE userID = _userID;
ELSE
SELECT NULL, 2 FROM DUAL;
END IF;


END $$
DELIMITER;

-- Devuelve el listado de estados del usuario
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_getUserStatus $$
CREATE PROCEDURE sp_getUserStatus()

BEGIN

SELECT userStatusID, userStatusName, userStatusDescription
FROM userStatus;


END $$
DELIMITER;

-- Actualiza el estado de un usuario
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_setUserStatusByUserByID $$
CREATE PROCEDURE sp_setUserStatusByUserByID(IN _userID int, IN _userStatusID int)

BEGIN

UPDATE users SET userStatusID = _userStatusID
WHERE userID = _userID;

END $$
DELIMITER;

-- Devuelve toda la nomina de usuarios
DELIMITER $$
DROP PROCEDURE IF EXISTS usr_getUsers $$
CREATE PROCEDURE usr_getUsers()

BEGIN

SELECT userID, 
       emailAddress
FROM users;



END $$
DELIMITER;

-- A partir de un nombre de usuario devuelve el ID
DELIMITER $$
DROP PROCEDURE IF EXISTS usr_getUserIdByName $$
CREATE PROCEDURE usr_getUserIdByName(IN _userName varchar(30))

BEGIN

SELECT userID
FROM usersInformation
WHERE userName = _userName;


END $$
DELIMITER;

-- A partir de un nombre de usuario devuelve el ID
DELIMITER $$
DROP PROCEDURE IF EXISTS usr_getUserIdByMail $$
CREATE PROCEDURE usr_getUserIdByMail(IN _emailAddress varchar(100))

BEGIN

SELECT userID
FROM quieroservicios.users
WHERE emailAddress = _emailAddress;


END $$
DELIMITER;

-- Incrementa el failCount de un usuario
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_increaseFailCountByUserID $$
CREATE PROCEDURE sp_increaseFailCountByUserID(IN _userID int)

BEGIN


SET @failCount = (SELECT failCount FROM users where userID = _userID);

UPDATE users SET failCount = @failCount+1 
WHERE userID = _userID;


END $$
DELIMITER;

-- Actualiza la password del usuario
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_setUserPassword $$
CREATE PROCEDURE sp_setUserPassword(IN _userID int, IN _newPassword varchar(50))

BEGIN

SET @oldPwdID = (SELECT userPasswordID FROM userPasswords where userID = _userID ORDER BY userPasswordID DESC LIMIT 1);

IF !ISNULL(@oldPwdID) THEN
UPDATE userPassowrds SET userPasswordStatusID = 2;
END IF;

INSERT INTO userpasswords 
(userID, userPassword, userPasswordStatusID, registerDate)
VALUES
(_userID, _newPassword, 1, CURRENT_TIMESTAMP());

END $$
DELIMITER;

-- Asocia un rol a un usuario
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_setUserRole $$
CREATE PROCEDURE sp_setUserRole(IN _userID int, IN _roleID int)

BEGIN

INSERT INTO userRoles
(userID, roleID)
VALUES
(_userID, _roleID);

END $$

DELIMITER;

-- Inserta la informaciòn basica de un usuario
DELIMITER $$
DROP PROCEDURE IF EXISTS usr_insertInformation $$
CREATE PROCEDURE usr_insertInformation (
											 IN _firstName 		varchar(100),
											 IN _lastName		varchar(100),
											 IN _birthDate 	varchar(20),
											 IN _emailAddress 	varchar(100),
											 IN _sexID int 
                        )
                                             

BEGIN

INSERT INTO users
(
firstName,
lastName,
birthDate,
emailAddress,
sexID,
registerDate
)

VALUES
(
_firstName,
_lastName, 
_birthDate,
_emailAddress,
_sexID,
now()
);


END $$

DELIMITER;

-- Inserta la informaciòn basica de un usuario
DELIMITER $$
DROP PROCEDURE IF EXISTS usr_insertMoreInformation $$
CREATE PROCEDURE usr_insertMoreInformation (
											 IN _userID			int,
											 IN _userName 		varchar(100)
                        )
                                             

BEGIN

IF exists (select userID from users where userID = _userID) THEN


	INSERT INTO usersInformation
	(
	userID,
	userName,
	registerDate
	)

	VALUES
	(
	_userID,
	_userName, 
	now()
	);
ELSE
	-- Error para indicar que no existe la clave primaria
    SIGNAL SQLSTATE '42000' SET MESSAGE_TEXT = 'Primary Key (userId) not exists.', MYSQL_ERRNO = 1171;
END IF;


END $$



