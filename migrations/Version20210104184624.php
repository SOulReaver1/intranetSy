<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210104184624 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client_statut (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_statut_document (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_DA496B245E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_statut_document_client_statut (client_statut_document_id INT NOT NULL, client_statut_id INT NOT NULL, INDEX IDX_625A5442D00C608E (client_statut_document_id), INDEX IDX_625A54421D00E80B (client_statut_id), PRIMARY KEY(client_statut_document_id, client_statut_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_files (id INT AUTO_INCREMENT NOT NULL, customer_statut_id INT DEFAULT NULL, client_statut_id_id INT NOT NULL, customer_source_id INT DEFAULT NULL, installer_id INT DEFAULT NULL, product_id INT DEFAULT NULL, sexe VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, zip_code INT DEFAULT NULL, home_phone VARCHAR(255) DEFAULT NULL, cellphone VARCHAR(20) DEFAULT NULL, referent_name VARCHAR(255) DEFAULT NULL, referent_phone VARCHAR(40) DEFAULT NULL, referent_statut VARCHAR(255) DEFAULT NULL, stairs TINYINT(1) DEFAULT NULL, mail_al VARCHAR(255) DEFAULT NULL, password_al VARCHAR(255) DEFAULT NULL, annex_quote TINYINT(1) DEFAULT NULL, description LONGTEXT DEFAULT NULL, commentary LONGTEXT DEFAULT NULL, route_number INT DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, address_complement VARCHAR(255) DEFAULT NULL, lng DOUBLE PRECISION DEFAULT NULL, lat DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_C80035625DDDC01E (customer_statut_id), INDEX IDX_C8003562FCB35328 (client_statut_id_id), INDEX IDX_C80035623EC1E47B (customer_source_id), INDEX IDX_C80035625A384417 (installer_id), INDEX IDX_C80035624584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_files_statut (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, color VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_E7301A075E237E06 (name), UNIQUE INDEX UNIQ_E7301A07665648E9 (color), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_source (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE files (id INT AUTO_INCREMENT NOT NULL, customer_files_id INT DEFAULT NULL, document_id INT DEFAULT NULL, file VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_63540598F231800 (customer_files_id), INDEX IDX_6354059C33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, statut_id INT DEFAULT NULL, description LONGTEXT NOT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, read_at DATETIME DEFAULT NULL, INDEX IDX_8875CAC9D86650F (user_id_id), INDEX IDX_8875CACF6203804 (statut_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help_statut (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_user (notification_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_35AF9D73EF1A9D84 (notification_id), INDEX IDX_35AF9D73A76ED395 (user_id), PRIMARY KEY(notification_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider_param (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider_product (id INT AUTO_INCREMENT NOT NULL, provider_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_2312A60AA53A8AA (provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider_product_provider_param (provider_product_id INT NOT NULL, provider_param_id INT NOT NULL, INDEX IDX_4BFB2F7160322AE2 (provider_product_id), INDEX IDX_4BFB2F711E05A68A (provider_param_id), PRIMARY KEY(provider_product_id, provider_param_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, statut_id INT NOT NULL, customer_file_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_97A0ADA361220EA6 (creator_id), INDEX IDX_97A0ADA3F6203804 (statut_id), INDEX IDX_97A0ADA38F16DDD2 (customer_file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket_user (ticket_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_BF48C371700047D2 (ticket_id), INDEX IDX_BF48C371A76ED395 (user_id), PRIMARY KEY(ticket_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket_message (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, from_user_id INT DEFAULT NULL, content VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_BA71692D700047D2 (ticket_id), INDEX IDX_BA71692D2130303A (from_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket_statut (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client_statut_document_client_statut ADD CONSTRAINT FK_625A5442D00C608E FOREIGN KEY (client_statut_document_id) REFERENCES client_statut_document (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client_statut_document_client_statut ADD CONSTRAINT FK_625A54421D00E80B FOREIGN KEY (client_statut_id) REFERENCES client_statut (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C80035625DDDC01E FOREIGN KEY (customer_statut_id) REFERENCES customer_files_statut (id)');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C8003562FCB35328 FOREIGN KEY (client_statut_id_id) REFERENCES client_statut (id)');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C80035623EC1E47B FOREIGN KEY (customer_source_id) REFERENCES customer_source (id)');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C80035625A384417 FOREIGN KEY (installer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE customer_files ADD CONSTRAINT FK_C80035624584665A FOREIGN KEY (product_id) REFERENCES provider_product (id)');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_63540598F231800 FOREIGN KEY (customer_files_id) REFERENCES customer_files (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_6354059C33F7837 FOREIGN KEY (document_id) REFERENCES client_statut_document (id)');
        $this->addSql('ALTER TABLE help ADD CONSTRAINT FK_8875CAC9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE help ADD CONSTRAINT FK_8875CACF6203804 FOREIGN KEY (statut_id) REFERENCES help_statut (id)');
        $this->addSql('ALTER TABLE notification_user ADD CONSTRAINT FK_35AF9D73EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_user ADD CONSTRAINT FK_35AF9D73A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE provider_product ADD CONSTRAINT FK_2312A60AA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
        $this->addSql('ALTER TABLE provider_product_provider_param ADD CONSTRAINT FK_4BFB2F7160322AE2 FOREIGN KEY (provider_product_id) REFERENCES provider_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE provider_product_provider_param ADD CONSTRAINT FK_4BFB2F711E05A68A FOREIGN KEY (provider_param_id) REFERENCES provider_param (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA361220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3F6203804 FOREIGN KEY (statut_id) REFERENCES ticket_statut (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA38F16DDD2 FOREIGN KEY (customer_file_id) REFERENCES customer_files (id)');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT FK_BF48C371700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT FK_BF48C371A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticket_message ADD CONSTRAINT FK_BA71692D700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticket_message ADD CONSTRAINT FK_BA71692D2130303A FOREIGN KEY (from_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client_statut_document_client_statut DROP FOREIGN KEY FK_625A54421D00E80B');
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C8003562FCB35328');
        $this->addSql('ALTER TABLE client_statut_document_client_statut DROP FOREIGN KEY FK_625A5442D00C608E');
        $this->addSql('ALTER TABLE files DROP FOREIGN KEY FK_6354059C33F7837');
        $this->addSql('ALTER TABLE files DROP FOREIGN KEY FK_63540598F231800');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA38F16DDD2');
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C80035625DDDC01E');
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C80035623EC1E47B');
        $this->addSql('ALTER TABLE help DROP FOREIGN KEY FK_8875CACF6203804');
        $this->addSql('ALTER TABLE notification_user DROP FOREIGN KEY FK_35AF9D73EF1A9D84');
        $this->addSql('ALTER TABLE provider_product DROP FOREIGN KEY FK_2312A60AA53A8AA');
        $this->addSql('ALTER TABLE provider_product_provider_param DROP FOREIGN KEY FK_4BFB2F711E05A68A');
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C80035624584665A');
        $this->addSql('ALTER TABLE provider_product_provider_param DROP FOREIGN KEY FK_4BFB2F7160322AE2');
        $this->addSql('ALTER TABLE ticket_user DROP FOREIGN KEY FK_BF48C371700047D2');
        $this->addSql('ALTER TABLE ticket_message DROP FOREIGN KEY FK_BA71692D700047D2');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3F6203804');
        $this->addSql('ALTER TABLE customer_files DROP FOREIGN KEY FK_C80035625A384417');
        $this->addSql('ALTER TABLE help DROP FOREIGN KEY FK_8875CAC9D86650F');
        $this->addSql('ALTER TABLE notification_user DROP FOREIGN KEY FK_35AF9D73A76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA361220EA6');
        $this->addSql('ALTER TABLE ticket_user DROP FOREIGN KEY FK_BF48C371A76ED395');
        $this->addSql('ALTER TABLE ticket_message DROP FOREIGN KEY FK_BA71692D2130303A');
        $this->addSql('DROP TABLE client_statut');
        $this->addSql('DROP TABLE client_statut_document');
        $this->addSql('DROP TABLE client_statut_document_client_statut');
        $this->addSql('DROP TABLE customer_files');
        $this->addSql('DROP TABLE customer_files_statut');
        $this->addSql('DROP TABLE customer_source');
        $this->addSql('DROP TABLE files');
        $this->addSql('DROP TABLE help');
        $this->addSql('DROP TABLE help_statut');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE notification_user');
        $this->addSql('DROP TABLE provider');
        $this->addSql('DROP TABLE provider_param');
        $this->addSql('DROP TABLE provider_product');
        $this->addSql('DROP TABLE provider_product_provider_param');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE ticket_user');
        $this->addSql('DROP TABLE ticket_message');
        $this->addSql('DROP TABLE ticket_statut');
        $this->addSql('DROP TABLE user');
    }
}
