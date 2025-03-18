<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231003121056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dropdown_menu ADD is_user_logout TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE service CHANGE is_active is_active TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE social_link CHANGE is_active is_active TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dropdown_menu DROP is_user_logout');
        $this->addSql('ALTER TABLE service CHANGE is_active is_active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE social_link CHANGE is_active is_active TINYINT(1) NOT NULL');
    }
}
