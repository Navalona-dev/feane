<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230927153903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dropdown_menu (id INT AUTO_INCREMENT NOT NULL, menu_header_id INT DEFAULT NULL, label VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, is_parameter TINYINT(1) DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, INDEX IDX_C95D51E237B978EC (menu_header_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE home_page (id INT AUTO_INCREMENT NOT NULL, background_color VARCHAR(255) DEFAULT NULL, background_image VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, homepage_button VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, label VARCHAR(255) DEFAULT NULL, button_color VARCHAR(255) DEFAULT NULL, button_color_hover VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE home_page_block (id INT AUTO_INCREMENT NOT NULL, home_page_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, home_page_block_button VARCHAR(255) DEFAULT NULL, background_color VARCHAR(255) DEFAULT NULL, label VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_94553904B966A8BC (home_page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_header (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, is_parameter TINYINT(1) DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site_configuration (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) DEFAULT NULL, favicon VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, copy_right VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE social_link (id INT AUTO_INCREMENT NOT NULL, site_configuration_id INT DEFAULT NULL, label VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, is_active TINYINT(1) DEFAULT NULL, INDEX IDX_79BD4A9544258CF6 (site_configuration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dropdown_menu ADD CONSTRAINT FK_C95D51E237B978EC FOREIGN KEY (menu_header_id) REFERENCES menu_header (id)');
        $this->addSql('ALTER TABLE home_page_block ADD CONSTRAINT FK_94553904B966A8BC FOREIGN KEY (home_page_id) REFERENCES home_page (id)');
        $this->addSql('ALTER TABLE social_link ADD CONSTRAINT FK_79BD4A9544258CF6 FOREIGN KEY (site_configuration_id) REFERENCES site_configuration (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dropdown_menu DROP FOREIGN KEY FK_C95D51E237B978EC');
        $this->addSql('ALTER TABLE home_page_block DROP FOREIGN KEY FK_94553904B966A8BC');
        $this->addSql('ALTER TABLE social_link DROP FOREIGN KEY FK_79BD4A9544258CF6');
        $this->addSql('DROP TABLE dropdown_menu');
        $this->addSql('DROP TABLE home_page');
        $this->addSql('DROP TABLE home_page_block');
        $this->addSql('DROP TABLE menu_header');
        $this->addSql('DROP TABLE site_configuration');
        $this->addSql('DROP TABLE social_link');
    }
}
