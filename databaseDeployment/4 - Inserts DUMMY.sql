/**
 *  USE THIS AFTER LOAD 5 SPs
 */


/**
 *  INSERT PROFESSIONAL
 */
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
   2 -- userID - IN int(11)
  ,1 -- userStatusID - IN int(11)
  ,'oak@uade.com' -- emailAddress - IN varchar(100)
  ,1   -- isAuthenticated - IN int(11)
  ,CURDATE()  -- authenticationDate - IN date
  ,CURDATE() -- registerDate - IN datetime
  ,CURDATE()  -- lastUpdate - IN datetime
  ,CURDATE()  -- lastLoggin - IN datetime
  ,0   -- failCount - IN tinyint(4)
);
UNLOCK TABLES;
/*!40000 ALTER TABLE users ENABLE KEYS */;

INSERT INTO userRoles VALUES (null, '2','4');
CALL pwd_insertUserPassword(2, 'oak'); -- _userID, _newPassword
CALL usi_insUserInformation(2, 'Samuel', 'Oak', '1970-12-12', 1, 11, '49999999'); -- (IN _userID int, IN _firstName varchar(100), IN _lastName varchar(100), IN _birthdate date, IN _sexID int, IN _areaCode int, IN _phoneNumber varchar(20))
CALL pfl_insertProfessional (2); -- _userID
CALL pps_insertProfessionalProfession(1, 1); -- _professionalID, _professionID
CALL pfl_insProfessionalLocation(2, 12, 3, 289, 'Av. Rivadavia 3270', -34.6107665, -58.4148294); -- (IN _userID int, IN _countryID int, IN _stateProvinceID int, IN _cityID int, in _streetAddress varchar(100), IN _lat int, IN _lng int)



/**
 *  INSERT CLIENT
 */
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
   3 -- userID - IN int(11)
  ,1 -- userStatusID - IN int(11)
  ,'carlitos@uade.com' -- emailAddress - IN varchar(100)
  ,1   -- isAuthenticated - IN int(11)
  ,CURDATE()  -- authenticationDate - IN date
  ,CURDATE() -- registerDate - IN datetime
  ,CURDATE()  -- lastUpdate - IN datetime
  ,CURDATE()  -- lastLoggin - IN datetime
  ,0   -- failCount - IN tinyint(4)
);
UNLOCK TABLES;
/*!40000 ALTER TABLE users ENABLE KEYS */;

INSERT INTO userRoles VALUES (null, '3','3');
CALL pwd_insertUserPassword(3, 'carlitos'); -- _userID, _newPassword
CALL usi_insUserInformation(3, 'Carlitos', 'Chuckles', '1970-12-12', 1, 11, '49999999'); -- (IN _userID int, IN _firstName varchar(100), IN _lastName varchar(100), IN _birthdate date, IN _sexID int, IN _areaCode int, IN _phoneNumber varchar(20))
CALL cli_insClientLocation(3, 12, 3, 289, 'Maza 302', -34.6145367, -58.4179678); -- (IN _userID int, IN _countryID int, IN _stateProvinceID int, IN _cityID int, in _streetAddress varchar(100), IN _lat int, IN _lng int)

