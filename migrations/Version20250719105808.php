<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250719105808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE annonce_categorie (annonce_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_3C5A3DA68805AB2F (annonce_id), INDEX IDX_3C5A3DA6BCF5E72D (categorie_id), PRIMARY KEY(annonce_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce_categorie ADD CONSTRAINT FK_3C5A3DA68805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce_categorie ADD CONSTRAINT FK_3C5A3DA6BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce_categorie DROP FOREIGN KEY FK_3C5A3DA68805AB2F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce_categorie DROP FOREIGN KEY FK_3C5A3DA6BCF5E72D
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE annonce_categorie
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE categorie
        SQL);
    }
}
