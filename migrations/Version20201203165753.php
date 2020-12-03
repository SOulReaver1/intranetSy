<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201203165753 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE customer_files_user');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer_files_user (customer_files_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F792D7AA8F231800 (customer_files_id), INDEX IDX_F792D7AAA76ED395 (user_id), PRIMARY KEY(customer_files_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE customer_files_user ADD CONSTRAINT FK_F792D7AA8F231800 FOREIGN KEY (customer_files_id) REFERENCES customer_files (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_files_user ADD CONSTRAINT FK_F792D7AAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }
}
