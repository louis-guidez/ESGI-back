<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528115149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_conversation ADD utilisateur_id INT DEFAULT NULL, ADD conversation_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_conversation ADD CONSTRAINT FK_E37C021AFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_conversation ADD CONSTRAINT FK_E37C021A9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E37C021AFB88E14F ON utilisateur_conversation (utilisateur_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E37C021A9AC0396 ON utilisateur_conversation (conversation_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_conversation DROP FOREIGN KEY FK_E37C021AFB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_conversation DROP FOREIGN KEY FK_E37C021A9AC0396
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_E37C021AFB88E14F ON utilisateur_conversation
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_E37C021A9AC0396 ON utilisateur_conversation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_conversation DROP utilisateur_id, DROP conversation_id
        SQL);
    }
}
