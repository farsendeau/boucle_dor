<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250502175524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE link (id INT AUTO_INCREMENT NOT NULL, site_id INT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, picto_name VARCHAR(255) DEFAULT NULL, INDEX IDX_36AC99F1F6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE site (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, code_postal VARCHAR(8) NOT NULL, city VARCHAR(32) NOT NULL, country VARCHAR(16) NOT NULL, map_url VARCHAR(255) DEFAULT NULL, map_image_name VARCHAR(255) DEFAULT NULL, tel VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE link ADD CONSTRAINT FK_36AC99F1F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE link DROP FOREIGN KEY FK_36AC99F1F6BD1646
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE link
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE site
        SQL);
    }
}
