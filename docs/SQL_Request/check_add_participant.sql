/* Vérifie que l'ajout d'un participant n'excède pas le nombre max autorisés pour la sortie concernée et que cette
   sortie se trouve bien dans un état qui lui permet de le faire.
 */

DROP TRIGGER IF EXISTS check_add_participant;

DELIMITER //

CREATE TRIGGER check_add_participant
	BEFORE INSERT
	ON participant_sortie
	FOR EACH ROW
BEGIN
	-- *****************************
	-- * Déclaration des variables *
	-- *****************************

	-- Constante (à défaut de mieux) ! Provient de l'entité Etat.
	DECLARE opened INTEGER DEFAULT 1;

	-- Nombre max de participants.
	DECLARE nombre_max INTEGER;

	-- Nombre de participants déjà inscrits.
	DECLARE nombre_participants INTEGER;

	-- Nom de la sortie à afficher en cas d'erreur.
	DECLARE nom_sortie VARCHAR(255);

	DECLARE date_inscription, date_debut DATETIME;
	DECLARE duree_sortie INTEGER;

	-- Variable recevant l'éventuel message d'erreur.
	DECLARE message VARCHAR(255);

	-- Variable recevant l'état actuel de la sortie
	DECLARE exit_state INTEGER DEFAULT 8;

	-- ***************************
	-- * Corps du déclencheur *
	-- ***************************

	-- Récupère le nombre de participants déjà inscrits.
	SELECT COUNT(*) FROM participant_sortie ps WHERE ps.sortie_id = new.sortie_id INTO nombre_participants;

	-- Récupère le nombre max de participants pouvant s'inscrire à la sortie.
	SELECT nb_inscriptions_max FROM sortie WHERE sortie.id = new.sortie_id INTO nombre_max;

	-- Si le nombre max est déjà atteint, génère une erreur et suspend l'ajout du participant.
	IF (nombre_participants >= nombre_max) THEN
		-- Récupère le nom de la sortie
		SELECT nom FROM sortie WHERE sortie.id = new.sortie_id INTO nom_sortie;

		-- Et affiche un message d'erreur relatif au nombre de participants dépassé.
		SET message = CONCAT('Erreur! Nombre max de participants atteint pour la sortie nommée ', nom_sortie, '(',
												 nombre_participants, '/', nombre_max, ')');
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = message;
	ELSE
		-- Examine l'état de la sortie pour savoir si elle peut accueillir d'autres participants.
		CALL check_sorties(new.sortie_id, FALSE, TRUE, TRUE, exit_state);

		IF (exit_state != opened) THEN
			-- Récupère le nom de la sortie
			SELECT nom, date_limite_inscription, date_heure_debut, duree
			FROM sortie
			WHERE sortie.id = new.sortie_id
			INTO nom_sortie, date_inscription, date_debut, duree_sortie;

			-- Et affiche un message d'erreur relatif à l'état de la sortie.
			SET message = CONCAT('Erreur! La sortie ', nom_sortie,
													 ' [', date_inscription, ' - ', date_debut, '/', duree_sortie, ']',
													 ' (', exit_state, ':', nombre_participants, '/', nombre_max, ')',
													 ' ne peut pas accueillir de participants');
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = message;
		END IF;
	END IF;
END //
