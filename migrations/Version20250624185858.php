<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250624185858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, gite_id INT NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, nb_adult INT NOT NULL, nb_child INT DEFAULT NULL, date_arrival DATE NOT NULL, date_departure DATE NOT NULL, total_price INT NOT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_E00CEDDE652CAE9B (gite_id), INDEX booking_dates_idx (date_arrival, date_departure), INDEX booking_status_idx (status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE652CAE9B FOREIGN KEY (gite_id) REFERENCES gite (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE652CAE9B
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE booking
        SQL);
    }
}
