-- Devuelve el listado de scores de cada profesional
DELIMITER $$
DROP PROCEDURE IF EXISTS cls_getProfessionalScores $$

CREATE PROCEDURE cls_getProfessionalScores()
BEGIN

SELECT u.firstName, u.lastName, s.ScoreDescription, ps.comments FROM professionalscores ps
inner join scores s ON ps.scoreID = s.ScoreID
INNER join clients c on c.clientID = ps.clientID
inner join userinformation u on c.userID = u.userID;

END $$

DELIMITER;

DELIMITER $$
DROP PROCEDURE IF EXISTS cls_insProfessionalScore $$

CREATE PROCEDURE cls_insProfessionalScore(IN _userId int, IN _scoreId int, IN _comments varchar(200))
BEGIN

set @clientId = (select clientid from clients where userid =_userId );

INSERT INTO professionalscores
(clientId, scoreid, comments)
values 
(@clientId, _scoreId,_comments);

END $$

DELIMITER;