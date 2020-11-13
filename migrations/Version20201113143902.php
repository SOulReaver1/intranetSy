<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201113143902 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_files ADD customer_source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C80035623EC1E47B FOREIGN KEY (customer_source_id) REFERENCES customer_source (id)');
        $this->addSql('CREATE INDEX IDX_C80035623EC1E47B ON customer_files (customer_source_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C80035623EC1E47B');
        $this->addSql('DROP INDEX IDX_C80035623EC1E47B ON customer_files');
        $this->addSql('ALTER TABLE customer_files DROP customer_source_id');
    }
}
