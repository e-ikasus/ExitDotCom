/* Vérifie l'état des sorties ou d'une sortie en particulier par rapport au nombre de participants, de la date courante
   et de l'état dans lequel se trouve chaque sortie. Cette vérification peut conduire ou pas à la modification des états
   et à l'actualisation d'un historique des opérations via la table 'log_debug'. Cette procédure devrait être exécutée
   avant chaque 'select' sur la liste des sorties.

   Liste des paramètres:

   exit_id      : Identifiant de la sortie à analyser ou 0 pour toutes les analyser.
   dbg          : Indique si les modifications doivent être sauvegardées dans la table 'log_debug'.
   simulate     : Active ou pas la simulation de la modification d'un état.
   append       : Indique si la table 'log_debug' doit être vider avant le traitement.
   return_state : Nvl état de la sortie, parmi ceux définis dans l'entité 'Etat'.
 */

DROP PROCEDURE IF EXISTS check_sorties;

DELIMITER //

CREATE PROCEDURE check_sorties(IN exit_id integer, IN dbg boolean, IN simulate boolean, IN append boolean,
															 INOUT return_state integer)
BEGIN
	-- *****************************
	-- * Déclaration des variables *
	-- *****************************

	-- Constantes (à défaut de mieux) ! Proviennent de l'entité Etat.
	DECLARE created INTEGER DEFAULT 0;
	DECLARE opened INTEGER DEFAULT 1;
	DECLARE closed INTEGER DEFAULT 2;
	DECLARE current INTEGER DEFAULT 3;
	DECLARE archived INTEGER DEFAULT 4;
	DECLARE canceled INTEGER DEFAULT 5;

	-- Variables recevant les identifiants des états de la base de données.
	DECLARE id_created INTEGER;
	DECLARE id_opened INTEGER;
	DECLARE id_closed INTEGER;
	DECLARE id_current INTEGER;
	DECLARE id_archived INTEGER;
	DECLARE id_canceled INTEGER;

	-- Variable pour détecter la position de fin du curseur.
	DECLARE the_end INTEGER DEFAULT 0;

	-- Variables recevant les valeurs des champs des sorties.
	DECLARE nom VARCHAR(255);
	DECLARE date_limit, date_start DATETIME;
	DECLARE is_not_empty, identifier, duration, nbr_max, nbr_cur, state INTEGER;

	-- Variable contenant l'id de l'ancien état.
	DECLARE old_state INTEGER;

	-- Date actuelle.
	DECLARE cur_date DATETIME DEFAULT NOW();

	-- Variable servant à recevoir le message pour le débogage.
	DECLARE comment VARCHAR(255);

	-- Curseur utilisé pour parcourir le résultat de la requête d'une sortie en particulier.
	DECLARE cursor_one_exit CURSOR FOR
		SELECT ps.sortie_id,
					 s.id,
					 s.nom,
					 s.date_heure_debut,
					 s.date_limite_inscription,
					 s.duree,
					 s.nb_inscriptions_max,
					 s.etat_id,
					 COUNT(*)
		FROM participant_sortie ps
					 RIGHT JOIN sortie s ON ps.sortie_id = s.id
		WHERE s.id = exit_id
		GROUP BY s.id;

	-- Curseur utilisé pour parcourir le résultat de la requête de toutes les sorties.
	DECLARE cursor_all_exit CURSOR FOR
		SELECT ps.sortie_id,
					 s.id,
					 s.nom,
					 s.date_heure_debut,
					 s.date_limite_inscription,
					 s.duree,
					 s.nb_inscriptions_max,
					 s.etat_id,
					 COUNT(*)
		FROM participant_sortie ps
					 RIGHT JOIN sortie s ON ps.sortie_id = s.id
		GROUP BY s.id;

	-- Déclare le gestionnaire NOT FOUND.
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET the_end = 1;

	-- *************************
	-- * Corps de la procédure *
	-- *************************

	-- Vide la table de débogage si demandé.
	IF ((NOT append) AND (dbg)) THEN DELETE FROM log_debug; END IF;

	-- Récupère les identifiants des états attribués par la base de données.
	SELECT id FROM etat WHERE id_libelle = created INTO id_created;
	SELECT id FROM etat WHERE id_libelle = opened INTO id_opened;
	SELECT id FROM etat WHERE id_libelle = closed INTO id_closed;
	SELECT id FROM etat WHERE id_libelle = current INTO id_current;
	SELECT id FROM etat WHERE id_libelle = archived INTO id_archived;
	SELECT id FROM etat WHERE id_libelle = canceled INTO id_canceled;

	-- Ouvre le curseur.
	IF (NOT exit_id) THEN OPEN cursor_all_exit; ELSE OPEN cursor_one_exit; END IF;

	-- Pour chaque ligne de la table 'sortie'.
	scan_sortie_list:
	LOOP
		-- Récupère une ligne.
		IF (NOT exit_id) THEN
			FETCH cursor_all_exit INTO is_not_empty, identifier, nom, date_start, date_limit, duration, nbr_max, state, nbr_cur;
		ELSE
			FETCH cursor_one_exit INTO is_not_empty, identifier, nom, date_start, date_limit, duration, nbr_max, state, nbr_cur;
		END IF;
		-- S'il n'y en a plus, alors c'est fini.
		IF the_end = 1 THEN
			LEAVE scan_sortie_list;
		END IF;

		-- Cas particulier où la sortie ne figure pas dans la table participant_sortie, car vide.
		IF (NOT is_not_empty) THEN SET nbr_cur = 1; END IF;

		-- L'état actuel deviendra donc l'ancien état après le traitement.
		SET old_state = state;

		-- Si la sortie est dans l'état 'ouverte'.
		IF (state = id_opened) THEN
			IF ((nbr_cur >= nbr_max) OR (cur_date > date_limit)) THEN
				SET state = id_closed;
			END IF;
		END IF;

		-- Si la sortie est dans l'état 'clôturée'.
		IF (state = id_closed) THEN
			IF ((nbr_cur < nbr_max) AND (cur_date <= date_limit)) THEN
				SET state = id_opened;
			ELSEIF (cur_date >= date_start) THEN
				SET state = id_current;
			END IF;
		END IF;

		-- Si la sortie est dans l'état 'en cours'.
		IF (state = id_current) THEN
			IF (cur_date > ADDDATE(date_start, INTERVAL duration MINUTE)) THEN
				SET state = id_closed;
			END IF;
		END IF;

		-- Si la sortie doit être archivée.
		IF (((state = id_closed) OR (state = id_canceled)) AND ((cur_date > ADDDATE(date_start, INTERVAL 1 MONTH)))) THEN
			SET state = id_archived;
		END IF;

		-- Convertit l'id de l'état de la BD en id de l'entité si une seule sortie est traitée.
		IF (exit_id) THEN
			IF (state = id_created) THEN
				SET return_state = created;
			ELSEIF (state = id_opened) THEN
				SET return_state = opened;
			ELSEIF (state = id_closed) THEN
				SET return_state = closed;
			ELSEIF (state = id_current) THEN
				SET return_state = current;
			ELSEIF (state = id_archived) THEN
				SET return_state = archived;
			ELSEIF (state = id_canceled) THEN
				SET return_state = canceled;
			END IF;
		END IF;

		-- Si l'état doit être modifié.
		IF (old_state != state) THEN
			-- Si le mode debug est actif, sauvegarde dans la BD ce qui doit être fait.
			IF (dbg) THEN
				SET comment = CONCAT(nom, ' : ', nbr_cur, ' / ', nbr_max, ' : ',
														 IF(old_state = id_created, 'CREATED',
																IF(old_state = id_opened, 'OPENED',
																	 IF(old_state = id_closed, 'CLOSED',
																			IF(old_state = id_current, 'CURRENT',
																				 IF(old_state = id_archived, 'ARCHIVED', 'CANCELED'))))),
														 ' => ',
														 IF(state = id_created, 'CREATED',
																IF(state = id_opened, 'OPENED',
																	 IF(state = id_closed, 'CLOSED',
																			IF(state = id_current, 'CURRENT',
																				 IF(state = id_archived, 'ARCHIVED', 'CANCELED'))))));

				INSERT INTO log_debug (date, sortie_id, ancien_etat_id, nouvel_etat_id, content)
				VALUES (cur_date, identifier, old_state, state, comment);
			END IF;

			IF (NOT simulate) THEN UPDATE sortie SET etat_id = state WHERE id = identifier; END IF;
		END IF;

	END LOOP scan_sortie_list;

	-- ferme le curseur.
	IF (NOT exit_id) THEN CLOSE cursor_all_exit; ELSE CLOSE cursor_one_exit; END IF;
END //
