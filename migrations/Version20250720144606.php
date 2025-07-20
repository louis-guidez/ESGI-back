<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250720144606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, reservation_id INT DEFAULT NULL, utilisateur_id INT DEFAULT NULL, stripe_intent_id VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) NOT NULL, currency VARCHAR(10) NOT NULL, status VARCHAR(255) DEFAULT NULL, INDEX IDX_723705D1B83297E7 (reservation_id), INDEX IDX_723705D1FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transaction ADD CONSTRAINT FK_723705D1B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transaction ADD CONSTRAINT FK_723705D1FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1B83297E7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1FB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE transaction
        SQL);
    }
}
