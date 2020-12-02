<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201202123128 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider_param DROP FOREIGN KEY FK_FDACC9FDA53A8AA');
        $this->addSql('DROP INDEX IDX_FDACC9FDA53A8AA ON provider_param');
        $this->addSql('ALTER TABLE provider_param DROP provider_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider_param ADD provider_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE provider_param ADD CONSTRAINT FK_FDACC9FDA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
        $this->addSql('CREATE INDEX IDX_FDACC9FDA53A8AA ON provider_param (provider_id)');
    }
}
