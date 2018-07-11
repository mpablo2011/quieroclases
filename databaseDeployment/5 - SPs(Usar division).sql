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
FROM users
WHERE userID = _userID;


END $$


 
-- Devuelve el listado de provincias
DELIMITER $$
DROP PROCEDURE IF EXISTS stp_getStateProvinces $$
CREATE PROCEDURE stp_getStateProvinces(IN _countryID int)

BEGIN

SELECT stateProvinceID, countryID, stateProvinceName
FROM stateprovinces
WHERE countryID = _countryID;


END $$


-- Dado un ID devuelve la descripcion de la provincia

DELIMITER $$
DROP PROCEDURE IF EXISTS stp_getStateProvinceByID $$
CREATE PROCEDURE stp_getStateProvinceByID(IN _stateProvinceID int)

BEGIN

SELECT stateProvinceID, stateProvinceName
FROM stateprovinces
WHERE stateProvinceID = _stateProvinceID;


END $$



-- Devuelve el listado de ciudades
DELIMITER $$
DROP PROCEDURE IF EXISTS cty_getCities $$
CREATE PROCEDURE cty_getCities()

BEGIN

SELECT cityID, cityName
FROM cities;


END $$


-- Dado un ID devuelve la descripcion de la ciudad
DELIMITER $$
DROP PROCEDURE IF EXISTS cty_getCityByID $$
CREATE PROCEDURE cty_getCityByID(IN _cityID int)

BEGIN

SELECT cityID, cityName
FROM cities
WHERE cityID = _cityID;


END $$




-- Devuelve el listado de ciudades asociadas a una provincia
DELIMITER $$
DROP PROCEDURE IF EXISTS stp_getCitesByStateProvinceID $$
CREATE PROCEDURE stp_getCitesByStateProvinceID(IN _stateProvinceID int)

BEGIN

SELECT stateProvinceID, cityID, cityName
FROM cities
WHERE stateProvinceID = _StateProvinceID;


END $$



-- Devuelve el listado de roles de usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS rle_getRoles $$
CREATE PROCEDURE rle_getRoles()

BEGIN

SELECT roleID, roleName, roleDescription
FROM roles
WHERE visibility = 1;


END $$



-- Devuelve el listado de roles de un usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS url_getUserRolesByUserID $$
CREATE PROCEDURE url_getUserRolesByUserID(IN _userID int)

BEGIN

SELECT userID, roleID
FROM userRoles
WHERE userID = _userID;

END $$



-- Devuelve el listado de estados del usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS sp_getUserStatus $$
CREATE PROCEDURE sp_getUserStatus()

BEGIN

SELECT userStatusID, userStatusName, userStatusDescription
FROM userStatus;


END $$


-- Actualiza el estado de un usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS sp_updateUserStatusByUserByID $$
CREATE PROCEDURE sp_updateUserStatusByUserByID(IN _userID int, IN _userStatusID int)

BEGIN

UPDATE users SET userStatusID = _userStatusID
WHERE userID = _userID;

END $$



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



-- Genera una nueva asociacion entre un profesional y una profesion

DELIMITER $$
DROP PROCEDURE IF EXISTS pps_insertProfessionalProfession $$
CREATE PROCEDURE pps_insertProfessionalProfession(IN _professionalID int, IN _professionID int)

BEGIN

INSERT INTO professionalProfessions
(professionalID, professionID)
VALUES
(_professionalID, _professionID);

END $$



-- Dado un userID devuelve el professionalID

DELIMITER $$
DROP PROCEDURE IF EXISTS sp_getProfessionalIDByUserID $$
CREATE PROCEDURE sp_getProfessionalIDByUserID(IN _userID int)

BEGIN

SELECT professionalID
FROM professionals
WHERE userID = _userID;


END $$


-- Inserto una nueva profesión

DELIMITER $$
DROP PROCEDURE IF EXISTS sp_insertProfession $$
CREATE PROCEDURE sp_insertProfession(IN _professionName varchar(50))
BEGIN

INSERT INTO professions
(professionName)
VALUES
(_professionName);

END $$


-- Actualizo una profesión

DELIMITER $$
DROP PROCEDURE IF EXISTS sp_updateProfession $$
CREATE PROCEDURE sp_updateProfession(IN _professionID int, IN _professionName varchar(50))
BEGIN

update professions set
professionName = _professionName
where professionID = _professionID;

END $$


-- Elimino una profesion

DELIMITER $$
DROP PROCEDURE IF EXISTS sp_deleteProfession $$
CREATE PROCEDURE sp_deleteProfession(IN _professionID int)
BEGIN

delete from professions where professionID = _professionID;

END $$


-- Obtengo el listado de ciudades asociadas a un Id de provincia
DELIMITER $$
DROP PROCEDURE IF EXISTS cty_getCitiesByStateProvinceID $$
CREATE PROCEDURE cty_getCitiesByStateProvinceID(IN _stateProvinceID int)
BEGIN

SELECT stateProvinceID, cityID, cityName
FROM cities
WHERE stateProvinceID = _StateProvinceID;

END $$



-- Obtengo una profesion en base a su ID

DELIMITER $$
DROP PROCEDURE IF EXISTS pfn_getProfessionByID $$
CREATE PROCEDURE pfn_getProfessionByID(IN _professionID int)
BEGIN

SELECT professionID, professionName
FROM professions
WHERE professionID = _professionID;

END $$



-- Obtengo el listado de profesiones

DELIMITER $$
DROP PROCEDURE IF EXISTS pfn_getProfessions $$
CREATE PROCEDURE pfn_getProfessions()
BEGIN

SELECT professionID, professionName
FROM professions;

END $$


--  Obtengo la informacion del usuario

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
  ,1 -- userStatusID - ACTIVO
  ,_usereMail -- emailAddress - IN varchar(100)
  ,1   -- isAuthenticated - IN int(11)
  ,CURDATE()  -- authenticationDate - IN date
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



--  Inserta una nueva contrasenia

DELIMITER $$
DROP PROCEDURE IF EXISTS pwd_insPassword $$
CREATE PROCEDURE pwd_insPassword(IN _userID int, IN _userPassword varchar(50))
BEGIN

INSERT INTO userpasswords(
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



--  Obtiene el listado de roles asociados a una ruta u objeto

DELIMITER $$
DROP PROCEDURE IF EXISTS rlg_getRoleByObject $$
CREATE PROCEDURE rlg_getRoleByObject(IN _object varchar(50))
BEGIN

SELECT roleID
FROM roleGrants
WHERE object = _object;


END $$


-- inserta o actualiza la informacion del usuario
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



DELIMITER $$
DROP PROCEDURE IF EXISTS prj_insProjectByUserID $$
CREATE PROCEDURE `prj_insProjectByUserID`(IN _userID int, IN _projectName varchar(255), _professionID int, _projectDescription text)
BEGIN

CREATE TEMPORARY TABLE prfID 
SELECT prf.professionalID 
FROM professionals prf, professionalprofessions prn, professionalLocation pfl,
     clients cli, clientlocation cll
WHERE prf.professionalID = prn.professionalID
AND cli.clientID = cll.clientID
AND prf.professionalID = pfl.professionalID
AND cll.countryID = pfl.countryID
AND cll.stateProvinceID = pfl.stateProvinceID
AND cll.cityID = pfl.cityID
AND prn.professionID = _professionID
AND cli.userID = _userID;

SET @existprofessionals = (select count(*) from prfID);

IF(@existprofessionals > 0)
THEN
    SET @clientID = (SELECT clientID FROM clients where userID = _userID);

    IF(@clientID is not null)
    THEN

      INSERT INTO projects(
         projectName
        ,clientID
        ,professionID
        ,registerDate
        ,projectStatusID
        ,projectDescription
      ) VALUES (
         _projectName
        ,@clientID -- clientID - IN int(11)
        ,_professionID -- professionID - IN int(11)
        ,CURRENT_TIMESTAMP()  -- registerDate - IN datetime
        ,1 -- projectStatusID - IN int(11)
        ,_projectDescription  -- projectDescription - IN text
      );
      
      SET @projectID = LAST_INSERT_ID();
      
      INSERT INTO projectprofessionals (projectID, professionalID)
      SELECT @projectID, professionalID FROM prfID;
      
      DROP TABLE prfID;
      
      SELECT @projectID as projectID FROM DUAL;

    END IF;
ELSE
      DROP TABLE prfID;  
      SELECT -1 as projectID FROM DUAL;
END IF;

END $$



DELIMITER $$

DROP PROCEDURE IF EXISTS prj_getProjectsByUserID $$
CREATE PROCEDURE prj_getProjectsByUserID(IN _userID int)
BEGIN

SELECT prj.projectID, prj.projectName projectName, prj.clientID clientID, prj.professionID professionID, 
prf.professionName professionName, prj.registerDate registerDate, prj.projectStatusID projectStatusID, 
prs.statusName statusName, prj.projectDescription projectDescription 
FROM projects prj, projectstatus prs, professions prf, clients cli
WHERE prj.projectStatusID = prs.projectStatusID
AND prj.professionID = prf.professionID
AND prj.clientID = cli.clientID
AND cli.userID = _userID;

END $$



-- Elimino un proyecto

DELIMITER $$
DROP PROCEDURE IF EXISTS prj_deleteProject $$
CREATE PROCEDURE prj_deleteProject(IN _userID int, IN _projectID int)
BEGIN

DELETE FROM projects 
WHERE clientID = (select clientID from clients where userID = _userID) 
AND projectID = _projectID;

END $$


-- Elimino un usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS usr_deleteUser $$
CREATE PROCEDURE usr_deleteUser(IN _userID int)
BEGIN

DELETE FROM users
WHERE userID = _userID;

END $$


-- Elimino las contrasenias del usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS usr_deleteUserPassword $$
CREATE PROCEDURE usr_deleteUserPassword(IN _userID int)
BEGIN

DELETE FROM usersPassword
WHERE userID = _userID;

END $$


-- Actualizo la posicion del usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS usi_updateUsrLatLgn $$
CREATE PROCEDURE usi_updateUsrLatLgn(IN _userID int, IN _lat FLOAT( 10, 6 ), _lgn FLOAT( 10, 6 ))
BEGIN

UPDATE usersinformation set lat = _lat, lng = _lng WHERE userID = _userID;

END $$



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



-- Inserta una notificacion pendiente

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



-- Actualiza el estado de una notificacion por user id y tipo de notificacion

DELIMITER $$
DROP PROCEDURE IF EXISTS ntf_updateNotificationStatus $$
CREATE PROCEDURE ntf_updateNotificationStatus(IN _userID int, _notificationType int, _newStatus int)
BEGIN

UPDATE notifications
SET notificationStatus = _newStatus
WHERE userID = _userID
AND notificationType = _notificationType;

END $$


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



-- Obtengo la informacion un usuario

DELIMITER $$
DROP PROCEDURE IF EXISTS usi_getUserInformation $$
CREATE PROCEDURE usi_getUserInformation(IN _userID int)
BEGIN

SELECT usi.usersInformationID, 
       usi.userID, 
       usi.firstName, 
       usi.lastName, 
       usi.birthdate, 
       usi.sexID,
       sex.sexName,
       usi.areaCode, 
       usi.phoneNumber 
FROM userinformation usi, sexTypes sex
WHERE usi.userID = _userID
AND sex.sexID = usi.sexID;

END $$


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


-- Obtengo la ubicacion del profesional
DELIMITER $$
DROP PROCEDURE IF EXISTS pfl_getProfessionalLocation $$
CREATE PROCEDURE pfl_getProfessionalLocation(IN _userID int)
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



-- inserta o actualiza la ubicacion de un cliente
DELIMITER $$
DROP PROCEDURE IF EXISTS cli_insClientLocation $$
CREATE PROCEDURE cli_insClientLocation(IN _userID int, IN _countryID int, IN _stateProvinceID int, IN _cityID int,
in _streetAddress varchar(100), IN _lat float, IN _lng float)
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


-- inserta o actualiza la ubicacion de un profesional
DELIMITER $$
DROP PROCEDURE IF EXISTS pfl_insProfessionalLocation $$
CREATE PROCEDURE pfl_insProfessionalLocation(IN _userID int, IN _countryID int, IN _stateProvinceID int, IN _cityID int,
in _streetAddress varchar(100), IN _lat float, IN _lng float)
BEGIN

SET @professionalID = (SELECT professionalID FROM professionals where userID = _userID);

IF(@professionalID is null)
THEN
INSERT INTO professionals (userID) VALUES (_userID);
SET @professionalID = (SELECT professionalID FROM professionals where userID = _userID);
END IF;

SET @professionalLocationID = (SELECT professionalLocationID FROM professionalLocation where professionalID = @professionalID);

IF(@professionalLocationID is null)
THEN

INSERT INTO professionalLocation(
   professionalID
  ,professionalLocationStatusID
  ,countryID
  ,stateProvinceID
  ,cityID
  ,streetAddress
  ,lat
  ,lng
) VALUES (
   @professionalID
  ,1
  ,_countryID   -- countryID - IN int(11)
  ,_StateProvinceID   -- stateProvinceID - IN int(11)
  ,_cityID   -- cityID - IN int(11)
  ,_streetAddress  -- streetAddress - IN varchar(100)
  ,_lat   -- lat - IN float(10,6)
  ,_lng   -- lng - IN float(10,6)
);

ELSE

UPDATE professionalLocation
SET countryID = _countryID -- int(11)
   ,stateProvinceID = _stateProvinceID -- int(11)
   ,cityID = _cityID -- int(11)
   ,streetAddress = _streetAddress -- varchar(100)
   ,lat = _lat -- float(10,6)
   ,lng = _lng -- float(10,6)
WHERE professionalLocationID = @professionalLocationID;

END IF;

END $$


-- Dado un projectID, devuelve los presupuestos.

DELIMITER $$
DROP PROCEDURE IF EXISTS bgt_getBudgetsByProjectID $$

CREATE PROCEDURE bgt_getBudgetsByProjectID(IN _projectID int)

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
       sty.sexName,
       pfl.stateProvinceID,
       pfl.cityID,
       pfl.streetAddress,
       pfl.lat,
       pfl.lng
FROM budgets bgt,
     budgetstatus bgs,
     professionals pfs,
     professionalLocation pfl,
     userinformation usi,
     sextypes sty
WHERE bgt.budgetStatusID = bgs.budgetStatusID
AND   pfs.professionalID = bgt.professionalID
AND pfs.userID = usi.userID
AND usi.sexID = sty.sexID
AND bgt.projectID = _projectID 
AND pfs.professionalID = pfl.professionalID;


END $$



-- Inserta un nuevo presupuesto

DELIMITER $$
DROP PROCEDURE IF EXISTS bgt_insBudget $$
CREATE PROCEDURE bgt_insBudget(IN _projectID int, IN _userID int, IN _amount bigint, IN _comments text)

BEGIN

SET @professionalID = (SELECT professionalID FROM professionals where userID = _userID);
IF(@professionalID is not null)
THEN

INSERT INTO budgets(
   projectID
  ,professionalID
  ,amount
  ,comments
) VALUES (
   _projectID -- projectID - IN int(11)
  ,@professionalID -- professionalID - IN int(11)
  ,_amount -- amount - IN bigint(20)
  ,_comments  -- comments - IN text
);

END IF;

END $$


DELIMITER $$
DROP PROCEDURE IF EXISTS prj_getProjectsByProfessionalID $$
CREATE PROCEDURE prj_getProjectsByProfessionalID(IN _userID int)
BEGIN

SELECT prj.projectID, 
       prj.projectName projectName, 
       prj.clientID clientID,
       usi.firstName,
       usi.lastName,
       cty.cityID,
       cty.cityName,
       stp.stateProvinceID,
       stp.stateProvinceName,
       cll.streetAddress,
       cll.lat,
       cll.lng,
       prj.professionID professionID, 
       prn.professionName professionName, 
       prj.registerDate registerDate, 
       prj.projectStatusID projectStatusID, 
       prs.statusName statusName, 
       prj.projectDescription projectDescription 
FROM projects prj, 
     projectstatus prs, 
     professions prn, 
     clients cli, 
     clientLocation cll, 
     userinformation usi, 
     professionals prf,
     projectprofessionals pjp,
     cities cty,
     stateprovinces stp
WHERE prj.projectStatusID = prs.projectStatusID
AND prj.clientID = cli.clientID
AND cli.userID = usi.userID
AND prj.professionID = prn.professionID
AND prj.projectID = pjp.projectID
AND pjp.professionalID = prf.professionalID
AND cll.clientID = cli.clientID
AND cll.stateProvinceID = stp.stateProvinceID
AND cll.cityID = cty.cityID
AND prf.userID = _userID;
END $$



-- Actualiza un presupuesto presupuesto

DELIMITER $$
DROP PROCEDURE IF EXISTS bgt_updateBudgetStatus $$
CREATE PROCEDURE bgt_updateBudgetStatus(IN _budgetID int, in _budgetStatusID int)

BEGIN
-- Si se acepta un presupuesto cambio el estado del proyecto y el del resto de los presupuestos
IF(_budgetStatusID = 2)
THEN

SET @projectID = (SELECT projectID FROM budgets where budgetID = _budgetID);

UPDATE budgets bgt 
SET bgt.budgetStatusID = 4
WHERE bgt.projectID = @projectID
AND bgt.budgetID <> _budgetID;

UPDATE budgets bgt 
SET bgt.budgetStatusID = _budgetStatusID
WHERE bgt.budgetID = _budgetID;

UPDATE projects SET projectStatusID = 4
WHERE projectID = @projectID;

ELSE

UPDATE budgets bgt 
SET bgt.budgetStatusID = _budgetStatusID
WHERE bgt.budgetID = _budgetID;

END IF;

END $$


DELIMITER $$
DROP PROCEDURE IF EXISTS prf_insProfessionalProfession $$
CREATE PROCEDURE prf_insProfessionalProfession(in _userID int, in _professionID int)
BEGIN

SET @professionalID = (SELECT professionalID FROM professionals WHERE userID = _userID);

SET @existprofessionalProfession = (select count(*) from professionalProfessions where professionalID = _userID and professionID = @professionalID);

IF(@existprofessionalProfession > 0)
THEN
      SELECT -1 as status FROM DUAL;
ELSE


      INSERT INTO professionalprofessions (
                             professionalID
                            ,professionID
                          ) VALUES (
                            @professionalID
                            ,_professionID
                          );
                          
      SELECT 1 as status FROM DUAL;
      
END IF;

END $$

-- Devuelve el listado de scores de cada cliente
DELIMITER $$
DROP PROCEDURE IF EXISTS cls_getClientScores $$

CREATE PROCEDURE cls_getClientScores(in _userID int)
BEGIN

SELECT cls.scoreID, cls.clientID, cls.projectID, cls.projectID,
       prj.projectName, prj.projectDescription, pfn.professionID, pfn.professionName,
       usi.firstName, usi.lastName
FROM clientscores cls, projects prj, professions pfn, userinformation usi, clients cli
WHERE cls.projectID = prj.projectID
AND prj.professionID = pfn.professionID
AND prj.clientID = cli.clientID
AND cli.userID = _userID
AND cls.clientID = cli.clientID; 

END $$

DELIMITER $$
DROP PROCEDURE IF EXISTS cls_insClientScore $$

CREATE PROCEDURE cls_insClientScore(IN _userID int, IN _scoreID int, IN _projectID int, IN _comments varchar(200))
BEGIN

SET @clientID = (SELECT clientID FROM clients WHERE userID = _userID);

INSERT INTO clientscores(
   scoreID
  ,clientID
  ,projectID
  ,comments
) VALUES (
   _scoreID
  ,@clientID
  , _projectID
  ,_comments
);

END $$

-- Devuelve el listado de scores de cada profesional
DELIMITER $$
DROP PROCEDURE IF EXISTS pfs_getProfessionalScores $$

CREATE PROCEDURE pfs_getProfessionalScores(IN _userID int)
BEGIN

SELECT pfs.scoreID, pfs.professionalID, pfs.projectID, pfs.comments,
       prj.projectName, prj.projectDescription, prj.professionID, pfn.professionName,
       usi.firstName, usi.lastName
FROM professionalscores pfs, professionals pro, projects prj, professions pfn, userinformation usi, clients cli
WHERE pfs.professionalID = pro.professionalID 
AND pfs.projectID = prj.projectID
AND prj.professionID = pfn.professionID
AND prj.clientID = cli.clientID
AND cli.userID = usi.userID
AND pro.userID = _userID;

END $$

DELIMITER $$

DROP PROCEDURE IF EXISTS pfs_insProfessionalScore $$

CREATE PROCEDURE pfs_insProfessionalScore(IN _userID int, IN _scoreID int, IN _projectID int, IN _comments varchar(200))
BEGIN

SET @professionalID = (SELECT professionalID FROM professionals WHERE userID = _userID);

INSERT INTO professionalscores
(professionalID, scoreID, projectID, comments)
VALUES
(@professionalID, _scoreID, _projectID, _comments);

END $$


-- Elimina una relacion profesional profesion

DELIMITER $$
DROP PROCEDURE IF EXISTS prf_deleteProfessionalProfession $$
CREATE PROCEDURE prf_deleteProfessionalProfession(IN _userID int, IN _professionID int)
BEGIN

SET @professionalID = (SELECT professionalID FROM professionals WHERE userID = _userID);

DELETE FROM professionalprofessions WHERE professionID = _professionID AND professionalID = @professionalID;

END $$


-- Obtiene las calificaciones pendientes del cliente

DELIMITER $$
DROP PROCEDURE IF EXISTS cls_getClientPendingScores $$
CREATE PROCEDURE cls_getClientPendingScores(IN _userID int)
BEGIN

SELECT 
  prj.projectID, 
  prj.projectName, 
  prj.projectDescription, 
  prf.professionID, 
  prf.professionName, 
  pro.professionalID, 
  ui.firstName, 
  ui.lastName
FROM projects prj 
INNER JOIN clients cli ON cli.clientID = prj.clientID 
INNER JOIN professions prf ON prf.professionID = prj.professionID 
INNER JOIN budgets b ON b.projectID = prj.projectID AND b.budgetStatusID IN (2,4)
INNER JOIN professionals pro ON pro.professionalID = b.professionalID 
INNER JOIN userinformation ui ON ui.userID = pro.userID 
WHERE cli.userID = _userID
AND prj.projectID NOT IN (SELECT projectID from clientScores)
AND prj.projectStatusID = 4;

END $$

-- Obtiene las calificaciones pendientes del profesional

DELIMITER $$
DROP PROCEDURE IF EXISTS cls_getPendingScores $$
CREATE PROCEDURE cls_getPendingScores(IN _userID int)
BEGIN

SELECT prj.projectID
FROM projects prj, budgets bgt
WHERE bgt.budgetStatusID = 1
AND prj.projectID NOT IN (SELECT projectID from clientScore)
AND prj.projectID = bgt.budgetID
AND bgt.professionalID = _professionalID
AND prj.projectStatusID = 4;

END $$

-- Obtiene las profesiones de un profesional

DELIMITER $$
DROP PROCEDURE IF EXISTS pfl_getProfessionalProfessions $$
CREATE PROCEDURE pfl_getProfessionalProfessions(IN _userID int)
BEGIN

SELECT pfn.professionID, pfn.professionName 
FROM professionals pro 
INNER JOIN professionalprofessions pfp ON pfp.professionalID = pro.professionalID
INNER JOIN professions pfn ON pfn.professionID = pfp.professionID 
WHERE pro.userID = _userID;

END $$