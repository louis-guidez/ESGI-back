<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250719134025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE utilisateur_annonce (utilisateur_id INT NOT NULL, annonce_id INT NOT NULL, INDEX IDX_8C5E6477FB88E14F (utilisateur_id), INDEX IDX_8C5E64778805AB2F (annonce_id), PRIMARY KEY(utilisateur_id, annonce_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_annonce ADD CONSTRAINT FK_8C5E6477FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_annonce ADD CONSTRAINT FK_8C5E64778805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_annonce DROP FOREIGN KEY FK_8C5E6477FB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_annonce DROP FOREIGN KEY FK_8C5E64778805AB2F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE utilisateur_annonce
        SQL);
    }
}
