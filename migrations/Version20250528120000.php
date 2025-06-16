<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add relation between photo and annonce';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE photo ADD annonce_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo ADD CONSTRAINT FK_3EAF8E95F675F31B FOREIGN KEY (annonce_id) REFERENCES annonce (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3EAF8E95F675F31B ON photo (annonce_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE photo DROP FOREIGN KEY FK_3EAF8E95F675F31B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_3EAF8E95F675F31B ON photo
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo DROP annonce_id
        SQL);
    }
}
