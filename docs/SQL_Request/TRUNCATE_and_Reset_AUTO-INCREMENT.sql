SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE log_debug;
TRUNCATE TABLE participant_sortie;
TRUNCATE TABLE sortie;
TRUNCATE TABLE participant;
TRUNCATE TABLE etat;
TRUNCATE TABLE lieu;
TRUNCATE TABLE campus;
TRUNCATE TABLE ville;
TRUNCATE TABLE reset_password_request;

SET FOREIGN_KEY_CHECKS = 1;

ALTER TABLE log_debug AUTO_INCREMENT = 1;
ALTER TABLE campus AUTO_INCREMENT = 1;
ALTER TABLE etat AUTO_INCREMENT = 1;
ALTER TABLE lieu AUTO_INCREMENT = 1;
ALTER TABLE participant AUTO_INCREMENT = 1;
ALTER TABLE participant_sortie AUTO_INCREMENT = 1;
ALTER TABLE sortie AUTO_INCREMENT = 1;
ALTER TABLE ville AUTO_INCREMENT = 1;
ALTER TABLE reset_password_request AUTO_INCREMENT = 1;