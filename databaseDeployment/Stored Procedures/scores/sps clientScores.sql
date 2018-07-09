-- Devuelve el listado de scores de cada cliente
DELIMITER $$
DROP PROCEDURE IF EXISTS cls_getClientScores $$

CREATE PROCEDURE cls_getClientScores()
BEGIN

SELECT u.firstName, u.lastName, s.ScoreDescription, cs.comments FROM clientscores cs
inner join scores s ON cs.scoreID = s.ScoreID
INNER join clients c on c.clientID = cs.clientID
inner join userinformation u on c.userID = u.userID;

END $$

DELIMITER;

DROP PROCEDURE IF EXISTS cls_insClientScore $$

CREATE PROCEDURE cls_insClientScore(IN _userId int, IN _scoreId int, IN _comments varchar(200))
BEGIN

set @clientId = (select clientid from clients where userid =_userId );

INSERT INTO clientScores
(clientId, scoreid, comments)
values 
(@clientId, _scoreId,_comments);

END $$

DELIMITER;