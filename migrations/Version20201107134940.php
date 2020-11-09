<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201107134940 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client_statut (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_files_user (customer_files_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F792D7AA8F231800 (customer_files_id), INDEX IDX_F792D7AAA76ED395 (user_id), PRIMARY KEY(customer_files_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_files_statut (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE files (id INT AUTO_INCREMENT NOT NULL, customer_files_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_63540598F231800 (customer_files_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_files_user ADD CONSTRAINT FK_F792D7AA8F231800 FOREIGN KEY (customer_files_id) REFERENCES customer_files (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_files_user ADD CONSTRAINT FK_F792D7AAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_63540598F231800 FOREIGN KEY (customer_files_id) REFERENCES customer_files (id)');
        $this->addSql('ALTER TABLE customer_files ADD customer_statut_id INT DEFAULT NULL, ADD client_statut_id_id INT DEFAULT NULL, ADD sexe VARCHAR(255) NOT NULL, ADD name VARCHAR(255) NOT NULL, ADD adresse VARCHAR(255) NOT NULL, ADD city VARCHAR(255) NOT NULL, ADD zip_code INT NOT NULL, ADD home_phone VARCHAR(255) NOT NULL, ADD cellphone VARCHAR(20) DEFAULT NULL, ADD referent_name VARCHAR(255) NOT NULL, ADD referent_phone VARCHAR(40) DEFAULT NULL, ADD referent_statut VARCHAR(255) DEFAULT NULL, ADD stairs TINYINT(1) NOT NULL, ADD mail_al VARCHAR(255) DEFAULT NULL, ADD password_al VARCHAR(255) DEFAULT NULL, ADD annex_quote TINYINT(1) NOT NULL, ADD annex_quote_description LONGTEXT DEFAULT NULL, ADD annex_quote_commentary LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C80035625DDDC01E FOREIGN KEY (customer_statut_id) REFERENCES customer_files_statut (id)');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C8003562FCB35328 FOREIGN KEY (client_statut_id_id) REFERENCES client_statut (id)');
        $this->addSql('CREATE INDEX IDX_C80035625DDDC01E ON customer_files (customer_statut_id)');
        $this->addSql('CREATE INDEX IDX_C8003562FCB35328 ON customer_files (client_statut_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C8003562FCB35328');
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C80035625DDDC01E');
        $this->addSql('DROP TABLE client_statut');
        $this->addSql('DROP TABLE customer_files_user');
        $this->addSql('DROP TABLE customer_files_statut');
        $this->addSql('DROP TABLE files');
        $this->addSql('DROP INDEX IDX_C80035625DDDC01E ON customer_files');
        $this->addSql('DROP INDEX IDX_C8003562FCB35328 ON customer_files');
        $this->addSql('ALTER TABLE customer_files DROP customer_statut_id, DROP client_statut_id_id, DROP sexe, DROP name, DROP adresse, DROP city, DROP zip_code, DROP home_phone, DROP cellphone, DROP referent_name, DROP referent_phone, DROP referent_statut, DROP stairs, DROP mail_al, DROP password_al, DROP annex_quote, DROP annex_quote_description, DROP annex_quote_commentary');
    }
}
