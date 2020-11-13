<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201113130630 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client_statut_document_client_statut (client_statut_document_id INT NOT NULL, client_statut_id INT NOT NULL, INDEX IDX_625A5442D00C608E (client_statut_document_id), INDEX IDX_625A54421D00E80B (client_statut_id), PRIMARY KEY(client_statut_document_id, client_statut_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client_statut_document_client_statut ADD CONSTRAINT FK_625A5442D00C608E FOREIGN KEY (client_statut_document_id) REFERENCES client_statut_document (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client_statut_document_client_statut ADD CONSTRAINT FK_625A54421D00E80B FOREIGN KEY (client_statut_id) REFERENCES client_statut (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE client_statut_document_client_statut');
    }
}
