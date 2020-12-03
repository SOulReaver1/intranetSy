<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201203165951 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_files ADD installer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C80035625A384417 FOREIGN KEY (installer_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C80035625A384417 ON customer_files (installer_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C80035625A384417');
        $this->addSql('DROP INDEX IDX_C80035625A384417 ON customer_files');
        $this->addSql('ALTER TABLE customer_files DROP installer_id');
    }
}
