<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250719174455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation DROP INDEX UNIQ_8A8E26E9E0EA908C, ADD INDEX IDX_8A8E26E9E0EA908C (utilisateur_b_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation DROP INDEX UNIQ_8A8E26E9F25F3F62, ADD INDEX IDX_8A8E26E9F25F3F62 (utilisateur_a_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation CHANGE utilisateur_a_id utilisateur_a_id INT NOT NULL, CHANGE utilisateur_b_id utilisateur_b_id INT NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation DROP INDEX IDX_8A8E26E9F25F3F62, ADD UNIQUE INDEX UNIQ_8A8E26E9F25F3F62 (utilisateur_a_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation DROP INDEX IDX_8A8E26E9E0EA908C, ADD UNIQUE INDEX UNIQ_8A8E26E9E0EA908C (utilisateur_b_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation CHANGE utilisateur_a_id utilisateur_a_id INT DEFAULT NULL, CHANGE utilisateur_b_id utilisateur_b_id INT DEFAULT NULL
        SQL);
    }
}
