<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250627174652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE equipment ADD name_en VARCHAR(255) DEFAULT NULL, ADD description_en VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gite ADD title_en VARCHAR(255) DEFAULT NULL, ADD summary_en VARCHAR(255) DEFAULT NULL, ADD description_en LONGTEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE services ADD summary_en VARCHAR(255) DEFAULT NULL, ADD description_en VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE services DROP summary_en, DROP description_en
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipment DROP name_en, DROP description_en
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gite DROP title_en, DROP summary_en, DROP description_en
        SQL);
    }
}
