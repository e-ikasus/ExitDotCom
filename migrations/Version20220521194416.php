<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220521194416 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('CREATE TABLE log_debug (id INT AUTO_INCREMENT NOT NULL, sortie_id INT DEFAULT NULL, ancien_etat_id INT DEFAULT NULL, nouvel_etat_id INT DEFAULT NULL, date datetime NOT NULL, content VARCHAR(255) NOT NULL, index idx_4cdbacfacc72d953 (sortie_id), index idx_4cdbacfaff72fada (ancien_etat_id), index idx_4cdbacfa84adc73e (nouvel_etat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
		$this->addSql('ALTER TABLE log_debug ADD CONSTRAINT fk_4cdbacfacc72d953 FOREIGN KEY (sortie_id) REFERENCES sortie (id)');
		$this->addSql('ALTER TABLE log_debug ADD CONSTRAINT fk_4cdbacfaff72fada FOREIGN KEY (ancien_etat_id) REFERENCES etat (id)');
		$this->addSql('ALTER TABLE log_debug ADD CONSTRAINT fk_4cdbacfa84adc73e FOREIGN KEY (nouvel_etat_id) REFERENCES etat (id)');

		$str = file_get_contents("docs/SQL_Request/check_sorties.sql");
		$this->addSql($str);
		$str = file_get_contents("docs/SQL_Request/check_add_participant.sql");
		$this->addSql($str);
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('DROP TABLE log_debug');

		$this->addSql('DROP PROCEDURE IF EXISTS check_sorties');
		$this->addSql('DROP TRIGGER IF EXISTS check_add_participant');
	}
}
