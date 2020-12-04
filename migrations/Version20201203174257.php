<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201203174257 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_files ADD product_id INT DEFAULT NULL, CHANGE stairs stairs TINYINT(1) DEFAULT NULL, CHANGE annex_quote annex_quote TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C80035624584665A FOREIGN KEY (product_id) REFERENCES provider_product (id)');
        $this->addSql('CREATE INDEX IDX_C80035624584665A ON customer_files (product_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C80035624584665A');
        $this->addSql('DROP INDEX IDX_C80035624584665A ON customer_files');
        $this->addSql('ALTER TABLE customer_files DROP product_id, CHANGE stairs stairs TINYINT(1) NOT NULL, CHANGE annex_quote annex_quote TINYINT(1) NOT NULL');
    }
}
