-- Dado un ID, devuelve la informacion del usuario.

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
 
-- Devuelve el listado de provincias
DELIMITER $$
DROP PROCEDURE IF EXISTS stp_getStateProvinces $$
CREATE PROCEDURE stp_getStateProvinces(IN _countryID int)

BEGIN

SELECT stateProvinceID, countryID, stateProvinceName
FROM stateprovinces
WHERE countryID = _countryID;


END $$
DELIMITER;

-- Dado un ID devuelve la descripcion de la provincia

DELIMITER $$
DROP PROCEDURE IF EXISTS stp_getStateProvinceByID $$
CREATE PROCEDURE stp_getStateProvinceByID(IN _stateProvinceID int)

BEGIN

SELECT stateProvinceID, stateProvinceName
FROM stateprovinces
WHERE stateProvinceID = _stateProvinceID;


END $$
DELIMITER;


-- Devuelve el listado de ciudades
DELIMITER $$
DROP PROCEDURE IF EXISTS cty_getCities $$
CREATE PROCEDURE cty_getCities()

BEGIN

SELECT cityID, cityName
FROM cities;


END $$
DELIMITER;

-- Dado un ID devuelve la descripcion de la ciudad
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
DROP PROCEDURE IF EXISTS stp_getCitesByStateProvinceID $$
CREATE PROCEDURE stp_getCitesByStateProvinceID(IN _stateProvinceID int)

BEGIN

SELECT stateProvinceID, cityID, cityName
FROM cities
WHERE stateProvinceID = _StateProvinceID;


END $$
DELIMITER;


-- Devuelve el listado de roles de usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS rle_getRoles $$
CREATE PROCEDURE rle_getRoles()

BEGIN

SELECT roleID, roleName, roleDescription
FROM roles
WHERE visibility = 1;


END $$
DELIMITER;


-- Devuelve el listado de roles de un usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS url_getUserRolesByUserID $$
CREATE PROCEDURE url_getUserRolesByUserID(IN _userID int)

BEGIN

SELECT userID, roleID
FROM userRoles
WHERE userID = _userID;

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
DROP PROCEDURE IF EXISTS sp_updateUserStatusByUserByID $$
CREATE PROCEDURE sp_updateUserStatusByUserByID(IN _userID int, IN _userStatusID int)

BEGIN

UPDATE users SET userStatusID = _userStatusID
WHERE userID = _userID;

END $$
DELIMITER;


-- Incrementa el failCount de un usuario ingresando por el mail del usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS usr_increaseFailCountByEmailID$$
CREATE PROCEDURE usr_increaseFailCountByEmailID(IN _userEmail varchar(100))
BEGIN

SET @userID = (SELECT userID FROM users where emailAddress = _usereMail);
IF(@userID is not null)
THEN

SET @failCount = (SELECT failCount FROM users where userID = @userID);

SET @failCount = @failCount+1;

UPDATE users SET failCount = @failCount
WHERE userID = @userID;

  IF(@failCount > 4)
  THEN
    UPDATE users SET userStatusID = 4
    WHERE userID = @userID;
  END IF;


END IF;

END $$
DELIMITER;


-- Incrementa el failCount de un usuario ingresando por el id del usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS usr_increaseFailCountByUserID$$
CREATE PROCEDURE usr_increaseFailCountByUserID(IN _userID int)
BEGIN

IF(_userID is not null)
THEN

SET @failCount = (SELECT failCount FROM users where userID = _userID);

SET @failCount = @failCount+1;

UPDATE users SET failCount = @failCount
WHERE userID = _userID;

  IF(@failCount > 4)
  THEN
    UPDATE users SET userStatusID = 4
    WHERE userID = _userID;
  END IF;


END IF;

END $$
DELIMITER;



-- Insterta la password del usuario. SI ya existe una password dada de alta la actualiza

DELIMITER $$
DROP PROCEDURE IF EXISTS pwd_insertUserPassword $$
CREATE PROCEDURE pwd_insertUserPassword(IN _userID int, IN _newPassword varchar(50))

BEGIN

SET @oldPwdID = (SELECT userPasswordID 
                 FROM userPasswords where userID = _userID
                 AND userPasswordStatusID = 1
                 ORDER BY userPasswordID DESC LIMIT 1);


IF (!ISNULL(@oldPwdID)) THEN

  IF(@oldPwdID = _newPassword) THEN
  UPDATE userPassowrds SET userPasswordStatusID = 2;

  INSERT INTO userpasswords 
  (userID, userPassword, userPasswordStatusID, registerDate)
  VALUES
  (_userID, _newPassword, 1, CURRENT_TIMESTAMP());
  END IF;

ELSE

  INSERT INTO userpasswords 
  (userID, userPassword, userPasswordStatusID, registerDate)
  VALUES
  (_userID, _newPassword, 1, CURRENT_TIMESTAMP());
  
END IF;

END $$
DELIMITER;


-- Asocia un rol a un usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS url_insertUserRole $$
CREATE PROCEDURE url_insertUserRole(IN _userID int, IN _roleID int)

BEGIN

INSERT INTO userRoles
(userID, roleID)
VALUES
(_userID, _roleID);

END $$
DELIMITER;


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
DROP PROCEDURE IF EXISTS pfl_insertProfessional $$
CREATE PROCEDURE pfl_insertProfessional(IN _userID int)

BEGIN

INSERT INTO professionals 
(userID)
VALUES
(_userID);

END $$
DELIMITER;

-- Inserta un nuevo cliente

DELIMITER $$
DROP PROCEDURE IF EXISTS cli_insertClient $$
CREATE PROCEDURE cli_insertClient(IN _userID int)

BEGIN

INSERT INTO clients 
(userID)
VALUES
(_userID);

END $$
DELIMITER;


-- Genera una nueva asociacion entre un profesional y un lugar de trabajo

DELIMITER $$
DROP PROCEDURE IF EXISTS sp_insertProfessionalWorkplace $$
CREATE PROCEDURE sp_insertProfessionalWorkplace(IN _professionalID int, IN _cityID int)

BEGIN

INSERT INTO professionalWorkplaces
(userID, cityID)
VALUES
(_userID, _cityID);

END $$
DELIMITER;


-- Genera una nueva asociacion entre un profesional y una profesion

DELIMITER $$
DROP PROCEDURE IF EXISTS sp_insertProfessionalProfession $$
CREATE PROCEDURE sp_insertProfessionalProfession(IN _professionalID int, IN _professionID int)

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

-- Inserto una nueva profesiÃ³n

DELIMITER $$
DROP PROCEDURE IF EXISTS sp_insertProfession $$
CREATE PROCEDURE sp_insertProfession(IN _professionName varchar(50))
BEGIN

INSERT INTO professions
(professionName)
VALUES
(_professionName);

END $$
DELIMITER;

-- Actualizo una profesiÃ³n

DELIMITER $$
DROP PROCEDURE IF EXISTS sp_updateProfession $$
CREATE PROCEDURE sp_updateProfession(IN _professionID int, IN _professionName varchar(50))
BEGIN

update professions set
professionName = _professionName
where professionID = _professionID;

END $$
DELIMITER;

-- Elimino una profesion

DELIMITER $$
DROP PROCEDURE IF EXISTS sp_deleteProfession $$
CREATE PROCEDURE sp_deleteProfession(IN _professionID int)
BEGIN

delete from professions where professionID = _professionID;

END $$
DELIMITER;

-- Obtengo el listado de ciudades asociadas a un Id de provincia
DELIMITER $$
DROP PROCEDURE IF EXISTS cty_getCitiesByStateProvinceID $$
CREATE PROCEDURE cty_getCitiesByStateProvinceID(IN _stateProvinceID int)
BEGIN

SELECT stateProvinceID, cityID, cityName
FROM cities
WHERE stateProvinceID = _StateProvinceID;

END $$
DELIMITER;


-- Obtengo una profesion en base a su ID

DELIMITER $$
DROP PROCEDURE IF EXISTS pfn_getProfessionByID $$
CREATE PROCEDURE pfn_getProfessionByID(IN _professionID int)
BEGIN

SELECT professionID, professionName
FROM professions
WHERE professionID = _professionID;

END $$
DELIMITER;


-- Obtengo el listado de profesiones

DELIMITER $$
DROP PROCEDURE IF EXISTS pfn_getProfessions $$
CREATE PROCEDURE pfn_getProfessions()
BEGIN

SELECT professionID, professionName
FROM professions;

END $$
DELIMITER;

--  Obtengo la información del usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS usr_getUser $$
CREATE PROCEDURE usr_getUser(IN _emailAddress varchar(100))
BEGIN

SELECT usr.userID userID, usr.userStatusID userStatusID, usr.isAuthenticated isAuthenticated, usr.failCount failCount, upw.userPassword userPassword
FROM users usr, userPasswords upw
WHERE  usr.userID = upw.userID
 AND    usr.emailAddress = _emailAddress 
 AND    upw.userPasswordStatusID = 1;

END $$
DELIMITER;


--  Inserto un nuevo usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS usr_insUser $$
CREATE PROCEDURE usr_insUser(IN _usereMail varchar(100))
BEGIN

SET @userID = (SELECT userID FROM users where emailAddress = _usereMail);
IF(@userID is null)
THEN

INSERT INTO users(
   userID
  ,userStatusID
  ,emailAddress
  ,isAuthenticated
  ,authenticationDate
  ,registerDate
  ,lastUpdate
  ,lastLoggin
  ,failCount
) VALUES (
   NULL -- userID - IN int(11)
  ,3 -- userStatusID - Pendiente
  ,_usereMail -- emailAddress - IN varchar(100)
  ,0   -- isAuthenticated - IN int(11)
  ,NULL  -- authenticationDate - IN date
  ,CURDATE() -- registerDate - IN datetime
  ,NULL  -- lastUpdate - IN datetime
  ,CURDATE()  -- lastLoggin - IN datetime
  ,0   -- failCount - IN tinyint(4)
);

SELECT userID FROM users where emailAddress = _usereMail;

ELSE

SELECT -1 userID FROM DUAL;

END IF;

END $$
DELIMITER;


--  Inserta una nueva contrasenia

DELIMITER $$
DROP PROCEDURE IF EXISTS pwd_insPassword $$
CREATE PROCEDURE pwd_insPassword(IN _userID int, IN _userPassword varchar(50))
BEGIN

INSERT INTO quieroservicios.userpasswords(
   userPasswordID
  ,userID
  ,userPasswordStatusID
  ,userPassword
  ,registerDate
) VALUES (
   NULL -- userPasswordID - IN int(11)
  ,_userID -- userID - IN int(11)
  ,1 -- userPasswordStatusID - IN int(11)
  ,_userPassword -- userPassword - IN varchar(50)
  ,CURDATE() -- registerDate - IN datetime
);

END $$
DELIMITER;


--  Obtiene el listado de roles asociados a una ruta u objeto

DELIMITER $$
DROP PROCEDURE IF EXISTS rlg_getRoleByObject $$
CREATE PROCEDURE rlg_getRoleByObject(IN _object varchar(50))
BEGIN

SELECT roleID
FROM roleGrants
WHERE object = _object;


END $$
DELIMITER;

-- inserta o actualiza la información del usuario
DELIMITER $$
DROP PROCEDURE IF EXISTS usi_insUserInformation $$
CREATE PROCEDURE usi_insUserInformation(IN _userID int, IN _firstName varchar(100), IN _lastName varchar(100), IN _birthdate date,
IN _sexID int, IN _areaCode int, IN _phoneNumber varchar(20))
BEGIN

SET @userID = (SELECT userID FROM userInformation where userID = _userID);
IF(@userID is null)
THEN

INSERT INTO userInformation(
   userID
  ,firstName
  ,lastName
  ,birthdate
  ,sexID
  ,areaCode
  ,phoneNumber
) VALUES (
   _userID -- userID - IN int(11)
  ,_firstName -- firstName - IN varchar(100)
  ,_lastName -- lastName - IN varchar(100)
  ,_birthdate -- birthdate - IN date
  ,_sexID -- sexID - IN int(11)
  ,_areaCode  -- areaCode - IN varchar(10)
  ,_phoneNumber  -- phoneNumber - IN varchar(20)
);

ELSE

UPDATE userInformation
SET
   firstName = _firstName -- varchar(100)
  ,lastName = _lastName -- varchar(100)
  ,birthdate = _birthdate -- date
  ,sexID = _sexID -- int(11)
  ,areaCode = _areaCode -- varchar(10)
  ,phoneNumber = _phoneNumber -- varchar(20)
WHERE userID = @userID;


END IF;

END $$
DELIMITER;


DELIMITER $$
DROP PROCEDURE IF EXISTS prj_insProjectByUserID $$
CREATE PROCEDURE `prj_insProjectByUserID`(IN _userID int, IN _projectName varchar(255), _professionID int, _projectDescription text)
BEGIN

SET @clientID = (SELECT clientID FROM clients where userID = _userID);
IF(@clientID is not null)
THEN

INSERT INTO projects(
   projectID
   ,projectName
  ,clientID
  ,professionID
  ,registerDate
  ,projectStatusID
  ,projectDescription
) VALUES (
   NULL -- projectID - IN int(11)
   ,_projectName
  ,@clientID -- clientID - IN int(11)
  ,_professionID -- professionID - IN int(11)
  ,CURRENT_TIMESTAMP()  -- registerDate - IN datetime
  ,1 -- projectStatusID - IN int(11)
  ,_projectDescription  -- projectDescription - IN text
);

END IF;

END $$
DELIMITER;



DELIMITER $$
DROP PROCEDURE IF EXISTS prj_getProjectsByUserID $$
CREATE PROCEDURE prj_getProjectsByUserID(IN _userID int)
BEGIN

SELECT prj.projectID, prj.projectName projectName, prj.clientID clientID, prj.professionID professionID, 
prf.professionName professionName, DATE_FORMAT(prj.registerDate, '%d/%m/%y') registerDate, prj.projectStatusID projectStatusID, 
prs.statusName statusName, prj.projectDescription projectDescription 
FROM projects prj, projectstatus prs, professions prf, clients cli
WHERE prj.projectStatusID = prs.projectStatusID
AND prj.professionID = prf.professionID
AND prj.clientID = cli.clientID
AND cli.userID = _userID;

END $$
DELIMITER;


-- Elimino un proyecto

DELIMITER $$
DROP PROCEDURE IF EXISTS prj_deleteProject $$
CREATE PROCEDURE prj_deleteProject(IN _userID int, IN _projectID int)
BEGIN

DELETE FROM projects 
WHERE clientID = (select clientID from clients where userID = _userID) 
AND projectID = _projectID;

END $$
DELIMITER;

-- Elimino un usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS usr_deleteUser $$
CREATE PROCEDURE usr_deleteUser(IN _userID int)
BEGIN

DELETE FROM users
WHERE userID = _userID;

END $$
DELIMITER;

-- Elimino las contrasenias del usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS usr_deleteUserPassword $$
CREATE PROCEDURE usr_deleteUserPassword(IN _userID int)
BEGIN

DELETE FROM usersPassword
WHERE userID = _userID;

END $$
DELIMITER;

-- Actualizo la posicion del usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS usi_updateUsrLatLgn $$
CREATE PROCEDURE usi_updateUsrLatLgn(IN _userID int, IN _lat FLOAT( 10, 6 ), _lgn FLOAT( 10, 6 ))
BEGIN

UPDATE usersinformation set lat = _lat, lng = _lng WHERE userID = _userID;

END $$
DELIMITER;


-- Inserta un nuevo User Auth Token

DELIMITER $$
DROP PROCEDURE IF EXISTS uat_insertUserAuthToken $$
CREATE PROCEDURE uat_insertUserAuthToken(IN _userID varchar(100), IN _authentTokenValue varchar(256))
BEGIN

UPDATE userauthenticationtoken SET tokenStatus = 2 WHERE userID = _userID;

INSERT INTO userauthenticationtoken(
   userAuthenticationTokenID
  ,userID
  ,registerDate
  ,authentTokenValue
  ,tokenStatus
) VALUES (
   NULL -- userAuthenticationTokenID - IN int(11)
  ,_userID -- emailAddress - IN varchar(100)
  ,CURRENT_TIMESTAMP() -- registerDate - IN datetime
  ,_authentTokenValue  -- authentTokenValue - IN varchar(256)
  ,1 -- tokenStatus - IN int(11)
);

END $$
DELIMITER;


-- Obtiene un AuthToken en base a un userID

DELIMITER $$
DROP PROCEDURE IF EXISTS uat_getUserAuthToken $$
CREATE PROCEDURE uat_getUserAuthToken(IN _userID varchar(100))
BEGIN

SELECT userAuthenticationTokenID,
       userID,
       registerDate,
       authentTokenValue,
       tokenStatus       
FROM userauthenticationtoken;

END $$
DELIMITER;


-- Inserta una notificación pendiente

DELIMITER $$
DROP PROCEDURE IF EXISTS ntf_insertNotification $$
CREATE PROCEDURE ntf_insertNotification(IN _userID int, _notificationType int)
BEGIN

INSERT INTO notifications(
  userID
  ,notificationType
) VALUES (
  _userID
  ,_notificationType
);

END $$
DELIMITER;

-- Devuelve el listado de notificaciones pendientes para envio de mails

DELIMITER $$
DROP PROCEDURE IF EXISTS ntf_getPendingNotifications $$
CREATE PROCEDURE ntf_getPendingNotifications(_notificationType int)
BEGIN

SELECT ntf.userID userID, usr.userStatusID userStatusID, usr.emailAddress emailAddress
FROM notifications ntf, users usr
WHERE ntf.notificationType = _notificationType
AND ntf.notificationStatus = 1
AND ntf.userID = usr.userID;

END $$
DELIMITER;


-- Actualiza el estado de una notificacion por user id y tipo de notificacion

DELIMITER $$
DROP PROCEDURE IF EXISTS ntf_updateNotificationStatus $$
CREATE PROCEDURE ntf_updateNotificationStatus(IN _userID int, _notificationType int, _newStatus int)
BEGIN

UPDATE quieroservicios.notifications
SET notificationStatus = _newStatus
WHERE userID = _userID
AND notificationType = _notificationType;

END $$
DELIMITER;

-- Permite autenticar a un usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS usr_authenticateUser $$
CREATE PROCEDURE usr_authenticateUser(IN _authentTokenValue varchar(256))
BEGIN

SET @userID = (SELECT userID FROM userAuthenticationToken WHERE authentTokenValue = _authentTokenValue
                                                          AND tokenStatus = 1);

SET @updated = 0;

IF(@userID is not null)
THEN
UPDATE users set userStatusID = 1, isAuthenticated = 1, authenticationDate = CURRENT_TIMESTAMP();

SET @updated = 1;

END IF;

IF (@updated = 1)
THEN

UPDATE userAuthenticationToken SET tokenStatus = 2 where userID = @userID AND tokenStatus = 1;

END IF;

END $$
DELIMITER;


-- Obtengo la informacion un usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS usi_getUserInformation $$
CREATE PROCEDURE usi_getUserInformation(IN _userID int)
BEGIN

SELECT usi.usersInformationID, 
       usi.userID, 
       usi.firstName, 
       usi.lastName, 
       DATE_FORMAT(usi.birthdate, '%d/%m/%y') AS birthdate, 
       usi.sexID,
       sex.sexName,
       usi.areaCode, 
       usi.phoneNumber 
FROM userinformation usi, sexTypes sex
WHERE usi.userID = _userID
AND sex.sexID = usi.sexID;

END $$
DELIMITER;

-- Obtengo informacion de la tabla sexTypes
DELIMITER $$
DROP PROCEDURE IF EXISTS sex_getSexTypes $$
CREATE PROCEDURE sex_getSexTypes()
BEGIN

SELECT  
sex.sexID,
sex.sexCode,
sex.SexName
FROM sextypes sex;

END $$
DELIMITER;


-- Obtengo la ubicacion del cliente
DELIMITER $$
DROP PROCEDURE IF EXISTS cli_getclientLocation $$
CREATE PROCEDURE cli_getclientLocation(IN _userID int)
BEGIN

SELECT 
cll.clientLocationID, 
cll.clientID, 
cll.countryID, 
cll.stateProvinceID, 
cll.cityID, 
cll.streetAddress,
cll.lat,
cll.lng
FROM clientlocation cll, clients cli
WHERE cll.clientID = cli.userID 
AND cli.userID = _userID;

END $$
DELIMITER;

-- Obtengo la ubicacion del profesional
DELIMITER $$
DROP PROCEDURE IF EXISTS pfl_getprofessionaltLocation $$
CREATE PROCEDURE pfl_getprofessionaltLocation(IN _userID int)
BEGIN

SELECT 
pfl.clientLocationID, 
pfl.clientID, 
pfl.countryID, 
pfl.pfl.stateProvinceID, 
pfl.cityID, 
pfl.streetAddress,
pfl.lat,
pfl.lng
FROM profesionalLocation pfl, professionals pfs
WHERE pfl.professionalID = pfs.professionalID
AND pfs.userID = _userID;

END $$
DELIMITER;


-- inserta o actualiza la ubicacion de un cliente
DELIMITER $$
DROP PROCEDURE IF EXISTS cli_insClientLocation $$
CREATE PROCEDURE cli_insClientLocation(IN _userID int, IN _countryID int, IN _stateProvinceID int, IN _cityID int,
in _streetAddress varchar(100), IN _lat int, IN _lng int)
BEGIN

SET @clientID = (SELECT clientID FROM clients where userID = _userID);

IF(@clientID is null)
THEN
INSERT INTO clients (userID) VALUES (_userID);
SET @clientID = (SELECT clientID FROM clients where userID = _userID);
END IF;

SET @clientLocationID = (SELECT clientLocationID FROM clientLocation where clientID = @clientID);

IF(@clientLocationID is null)
THEN

INSERT INTO clientlocation(
   clientID
  ,clientLocationStatusID
  ,countryID
  ,stateProvinceID
  ,cityID
  ,streetAddress
  ,lat
  ,lng
) VALUES (
   @clientID -- clientID - IN int(11)
  ,1 -- clientLocationStatusID - IN int(11)
  ,_countryID   -- countryID - IN int(11)
  ,_StateProvinceID   -- stateProvinceID - IN int(11)
  ,_cityID   -- cityID - IN int(11)
  ,_streetAddress  -- streetAddress - IN varchar(100)
  ,_lat   -- lat - IN float(10,6)
  ,_lng   -- lng - IN float(10,6)
);

ELSE

UPDATE clientlocation
SET countryID = _countryID -- int(11)
   ,stateProvinceID = _stateProvinceID -- int(11)
   ,cityID = _cityID -- int(11)
   ,streetAddress = _streetAddress -- varchar(100)
   ,lat = _lat -- float(10,6)
   ,lng = _lng -- float(10,6)
WHERE clientLocationID = @clientLocationID;

END IF;

END $$
DELIMITER;

-- Dado un projectID, devuelve los presupuestos.

DELIMITER $$
DROP PROCEDURE IF EXISTS bgt_getBudgetsByByProjectID $$

CREATE PROCEDURE bgt_getBudgetsByByProjectID(IN _projectID int)

BEGIN

SELECT bgt.budgetID, 
       bgt.projectID, 
       bgt.professionalID, 
       bgt.amount, 
       bgt.requestDate, 
       bgt.budgetStatusID,
       bgs.statusName,
       bgs.statusDescription,
       bgt.comments,
       usi.firstName,
       usi.lastName,
       usi.sexID,
       sty.sexCode,
       sty.sexName
FROM budgets bgt,
     budgetstatus bgs,
     professionals pfs,
     userinformation usi,
     sextypes sty
WHERE bgt.budgetStatusID = bgs.budgetStatusID
AND   pfs.professionalID = bgt.professionalID
AND pfs.userID = usi.userID
AND usi.sexID = sty.sexID;


END $$

DELIMITER;