<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211006145527 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C80035625A384417');
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C8003562B03A8386');
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C8003562B3B7F856');
        $this->addSql('ALTER TABLE customer_files ADD email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C80035625A384417 FOREIGN KEY (installer_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C8003562B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C8003562B3B7F856 FOREIGN KEY (metreur_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA361220EA6');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA361220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C80035625A384417');
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C8003562B03A8386');
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C8003562B3B7F856');
        $this->addSql('ALTER TABLE customer_files DROP email');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C80035625A384417 FOREIGN KEY (installer_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C8003562B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C8003562B3B7F856 FOREIGN KEY (metreur_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA361220EA6');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA361220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
