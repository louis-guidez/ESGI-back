<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528114526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE utilisateur_conversation (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation ADD utilisateur_conversation_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E91C75BCE0 FOREIGN KEY (utilisateur_conversation_id) REFERENCES utilisateur_conversation (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8A8E26E91C75BCE0 ON conversation (utilisateur_conversation_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur ADD utilisateur_conversation_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B31C75BCE0 FOREIGN KEY (utilisateur_conversation_id) REFERENCES utilisateur_conversation (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1D1C63B31C75BCE0 ON utilisateur (utilisateur_conversation_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E91C75BCE0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B31C75BCE0
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE utilisateur_conversation
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_8A8E26E91C75BCE0 ON conversation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation DROP utilisateur_conversation_id
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_1D1C63B31C75BCE0 ON utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur DROP utilisateur_conversation_id
        SQL);
    }
}
