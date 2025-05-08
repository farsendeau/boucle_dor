<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250508084425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE legal_page (id INT AUTO_INCREMENT NOT NULL, about_title_fr VARCHAR(255) DEFAULT NULL, about_title_en VARCHAR(255) DEFAULT NULL, about_content_fr LONGTEXT DEFAULT NULL, about_content_en LONGTEXT DEFAULT NULL, gcs_title_fr VARCHAR(255) DEFAULT NULL, gcs_title_en VARCHAR(255) DEFAULT NULL, gcs_content_fr LONGTEXT DEFAULT NULL, gcs_content_en LONGTEXT DEFAULT NULL, legal_notice_title_fr VARCHAR(255) DEFAULT NULL, legal_notice_title_en VARCHAR(255) DEFAULT NULL, legal_notice_content_fr LONGTEXT DEFAULT NULL, legal_notice_content_en LONGTEXT DEFAULT NULL, personal_data_title_fr VARCHAR(255) DEFAULT NULL, personal_data_title_en VARCHAR(255) DEFAULT NULL, personal_data_content_fr LONGTEXT DEFAULT NULL, personal_data_content_en LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE legal_page
        SQL);
    }
}
