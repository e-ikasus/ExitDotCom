<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220525085134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE groupe_prive ADD createur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE groupe_prive ADD CONSTRAINT FK_A8D00A9D73A201E5 FOREIGN KEY (createur_id) REFERENCES participant (id)');
        $this->addSql('CREATE INDEX IDX_A8D00A9D73A201E5 ON groupe_prive (createur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE groupe_prive DROP FOREIGN KEY FK_A8D00A9D73A201E5');
        $this->addSql('DROP INDEX IDX_A8D00A9D73A201E5 ON groupe_prive');
        $this->addSql('ALTER TABLE groupe_prive DROP createur_id');
    }
}
