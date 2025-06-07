<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528114231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur ADD nom VARCHAR(255) DEFAULT NULL, ADD prenom VARCHAR(255) DEFAULT NULL, ADD date_inscription DATETIME DEFAULT NULL, ADD cagnotte NUMERIC(10, 2) DEFAULT NULL, ADD email_is_verified TINYINT(1) DEFAULT NULL, ADD adresse VARCHAR(255) DEFAULT NULL, ADD postal_code INT DEFAULT NULL, ADD ville VARCHAR(255) DEFAULT NULL, ADD pays VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur DROP nom, DROP prenom, DROP date_inscription, DROP cagnotte, DROP email_is_verified, DROP adresse, DROP postal_code, DROP ville, DROP pays
        SQL);
    }
}
