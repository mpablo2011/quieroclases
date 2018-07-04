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
  ,2 -- userStatusID - IN int(11)
  ,'test@test.com' -- emailAddress - IN varchar(100)
  ,1   -- isAuthenticated - IN int(11)
  ,CURDATE()  -- authenticationDate - IN date
  ,CURDATE() -- registerDate - IN datetime
  ,CURDATE()  -- lastUpdate - IN datetime
  ,CURDATE()  -- lastLoggin - IN datetime
  ,0   -- failCount - IN tinyint(4)
);
UNLOCK TABLES;
/*!40000 ALTER TABLE users ENABLE KEYS */;


CALL url_setUserRole(1, 1);
CALL url_setUserRole(1, 3);
CALL url_setUserRole(1, 4);
CALL url_setUserRole(2, 4);

CALL pwd_insertUserPassword(1, 'biohazard2');

CALL pfl_insertProfessional (1);

CALL pps_insertProfessionalProfession(1, 1);

