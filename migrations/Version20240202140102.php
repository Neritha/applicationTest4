<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240202140102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier ADD premiere_donnee_id INT DEFAULT NULL, ADD id_premiere_donnee VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551F56705B3C FOREIGN KEY (premiere_donnee_id) REFERENCES donnee (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9B76551F56705B3C ON fichier (premiere_donnee_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551F56705B3C');
        $this->addSql('DROP INDEX UNIQ_9B76551F56705B3C ON fichier');
        $this->addSql('ALTER TABLE fichier DROP premiere_donnee_id, DROP id_premiere_donnee');
    }
}
