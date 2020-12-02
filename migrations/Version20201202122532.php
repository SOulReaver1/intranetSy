<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201202122532 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE provider_param (id INT AUTO_INCREMENT NOT NULL, provider_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_FDACC9FDA53A8AA (provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider_product_provider_param (provider_product_id INT NOT NULL, provider_param_id INT NOT NULL, INDEX IDX_4BFB2F7160322AE2 (provider_product_id), INDEX IDX_4BFB2F711E05A68A (provider_param_id), PRIMARY KEY(provider_product_id, provider_param_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE provider_param ADD CONSTRAINT FK_FDACC9FDA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
        $this->addSql('ALTER TABLE provider_product_provider_param ADD CONSTRAINT FK_4BFB2F7160322AE2 FOREIGN KEY (provider_product_id) REFERENCES provider_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE provider_product_provider_param ADD CONSTRAINT FK_4BFB2F711E05A68A FOREIGN KEY (provider_param_id) REFERENCES provider_param (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider_product_provider_param DROP FOREIGN KEY FK_4BFB2F711E05A68A');
        $this->addSql('DROP TABLE provider_param');
        $this->addSql('DROP TABLE provider_product_provider_param');
    }
}
