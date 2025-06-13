<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612195310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE equipment (id INT AUTO_INCREMENT NOT NULL, gite_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, picto_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_D338D583652CAE9B (gite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE gite_image (id INT AUTO_INCREMENT NOT NULL, gite_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_2F22F5C8652CAE9B (gite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipment ADD CONSTRAINT FK_D338D583652CAE9B FOREIGN KEY (gite_id) REFERENCES gite (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gite_image ADD CONSTRAINT FK_2F22F5C8652CAE9B FOREIGN KEY (gite_id) REFERENCES gite (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gite ADD updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE link ADD updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site ADD updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE equipment DROP FOREIGN KEY FK_D338D583652CAE9B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gite_image DROP FOREIGN KEY FK_2F22F5C8652CAE9B
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE equipment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE gite_image
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE link DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gite DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site DROP updated_at
        SQL);
    }
}
