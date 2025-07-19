<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250718165713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce ADD utilisateur_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F65593E5FB88E14F ON annonce (utilisateur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD conversation_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B6BD307F9AC0396 ON message (conversation_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD annonce_id INT DEFAULT NULL, ADD utilisateur_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C849558805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C84955FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C849558805AB2F ON reservation (annonce_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C84955FB88E14F ON reservation (utilisateur_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5FB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F65593E5FB88E14F ON annonce
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce DROP utilisateur_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C849558805AB2F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955FB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_42C849558805AB2F ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_42C84955FB88E14F ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP annonce_id, DROP utilisateur_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B6BD307F9AC0396 ON message
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP conversation_id
        SQL);
    }
}
