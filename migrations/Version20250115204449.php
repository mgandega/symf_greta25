<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250115204449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, conference_id INT DEFAULT NULL, pseudo VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, date DATETIME NOT NULL, INDEX IDX_67F068BC604B8382 (conference_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC604B8382 FOREIGN KEY (conference_id) REFERENCES conference (id)');
        $this->addSql('ALTER TABLE conference ADD nb_reservation INT NOT NULL');
        $this->addSql('ALTER TABLE image DROP file');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC604B8382');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('ALTER TABLE conference DROP nb_reservation');
        $this->addSql('ALTER TABLE image ADD file VARCHAR(255) NOT NULL');
    }
}
