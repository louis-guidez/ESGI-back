<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250719162939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_conversation DROP FOREIGN KEY FK_E37C021A9AC0396
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_conversation DROP FOREIGN KEY FK_E37C021AFB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE utilisateur_conversation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message CHANGE conversation_id conversation_id INT NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE utilisateur_conversation (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, conversation_id INT DEFAULT NULL, INDEX IDX_E37C021AFB88E14F (utilisateur_id), INDEX IDX_E37C021A9AC0396 (conversation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_conversation ADD CONSTRAINT FK_E37C021A9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_conversation ADD CONSTRAINT FK_E37C021AFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message CHANGE conversation_id conversation_id INT DEFAULT NULL
        SQL);
    }
}
