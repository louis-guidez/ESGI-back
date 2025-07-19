<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250719140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add participants columns to conversation and drop utilisateur_conversation table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DROP TABLE utilisateur_conversation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation ADD participant1_id INT NOT NULL, ADD participant2_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation ADD CONSTRAINT FK_CONVERSATION_PARTICIPANT1 FOREIGN KEY (participant1_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation ADD CONSTRAINT FK_CONVERSATION_PARTICIPANT2 FOREIGN KEY (participant2_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CONVERSATION_PARTICIPANT1 ON conversation (participant1_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CONVERSATION_PARTICIPANT2 ON conversation (participant2_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation ADD CONSTRAINT UNIQ_PARTICIPANTS UNIQUE (participant1_id, participant2_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message CHANGE conversation_id conversation_id INT NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE message CHANGE conversation_id conversation_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation DROP FOREIGN KEY FK_CONVERSATION_PARTICIPANT1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation DROP FOREIGN KEY FK_CONVERSATION_PARTICIPANT2
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_CONVERSATION_PARTICIPANT1 ON conversation
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_CONVERSATION_PARTICIPANT2 ON conversation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation DROP INDEX UNIQ_PARTICIPANTS
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation DROP participant1_id, DROP participant2_id
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE utilisateur_conversation (
                id INT AUTO_INCREMENT NOT NULL,
                utilisateur_id INT DEFAULT NULL,
                conversation_id INT DEFAULT NULL,
                INDEX IDX_E37C021AFB88E14F (utilisateur_id),
                INDEX IDX_E37C021A9AC0396 (conversation_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_conversation ADD CONSTRAINT FK_E37C021AFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_conversation ADD CONSTRAINT FK_E37C021A9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)
        SQL);
    }
}
