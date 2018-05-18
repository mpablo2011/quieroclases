
-- Inserta un nuevo cliente
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_setClient $$
CREATE PROCEDURE sp_setClient(IN _userID int)

BEGIN

INSERT INTO clients 
(userID)
VALUES
(_userID);

END $$
