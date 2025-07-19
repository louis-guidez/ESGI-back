<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250719172836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation ADD utilisateur_a_id INT DEFAULT NULL, ADD utilisateur_b_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9F25F3F62 FOREIGN KEY (utilisateur_a_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9E0EA908C FOREIGN KEY (utilisateur_b_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8A8E26E9F25F3F62 ON conversation (utilisateur_a_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8A8E26E9E0EA908C ON conversation (utilisateur_b_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9F25F3F62
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9E0EA908C
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_8A8E26E9F25F3F62 ON conversation
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_8A8E26E9E0EA908C ON conversation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation DROP utilisateur_a_id, DROP utilisateur_b_id
        SQL);
    }
}
