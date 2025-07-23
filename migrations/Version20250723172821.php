<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250723172821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce RENAME INDEX idx_annonce_utilisateur TO IDX_F65593E5FB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce_categorie RENAME INDEX idx_annonce_categorie_annonce TO IDX_3C5A3DA68805AB2F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce_categorie RENAME INDEX idx_annonce_categorie_categorie TO IDX_3C5A3DA6BCF5E72D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation RENAME INDEX idx_conv_a TO IDX_8A8E26E9F25F3F62
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation RENAME INDEX idx_conv_b TO IDX_8A8E26E9E0EA908C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message RENAME INDEX idx_message_sender TO IDX_B6BD307FF624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message RENAME INDEX idx_message_receiver TO IDX_B6BD307FCD53EDB6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message RENAME INDEX idx_message_conv TO IDX_B6BD307F9AC0396
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo RENAME INDEX idx_photo_annonce TO IDX_14B784188805AB2F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD stripe_amount DOUBLE PRECISION DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation RENAME INDEX idx_reservation_annonce TO IDX_42C849558805AB2F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation RENAME INDEX idx_reservation_utilisateur TO IDX_42C84955FB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_annonce RENAME INDEX idx_ua_annonce TO IDX_8C5E64778805AB2F
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce RENAME INDEX idx_f65593e5fb88e14f TO IDX_ANNONCE_UTILISATEUR
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message RENAME INDEX idx_b6bd307f9ac0396 TO IDX_MESSAGE_CONV
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message RENAME INDEX idx_b6bd307fcd53edb6 TO IDX_MESSAGE_RECEIVER
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message RENAME INDEX idx_b6bd307ff624b39d TO IDX_MESSAGE_SENDER
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE photo RENAME INDEX idx_14b784188805ab2f TO IDX_PHOTO_ANNONCE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation RENAME INDEX idx_8a8e26e9e0ea908c TO IDX_CONV_B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation RENAME INDEX idx_8a8e26e9f25f3f62 TO IDX_CONV_A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP stripe_amount
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation RENAME INDEX idx_42c84955fb88e14f TO IDX_RESERVATION_UTILISATEUR
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation RENAME INDEX idx_42c849558805ab2f TO IDX_RESERVATION_ANNONCE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce_categorie RENAME INDEX idx_3c5a3da6bcf5e72d TO IDX_ANNONCE_CATEGORIE_CATEGORIE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE annonce_categorie RENAME INDEX idx_3c5a3da68805ab2f TO IDX_ANNONCE_CATEGORIE_ANNONCE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur_annonce RENAME INDEX idx_8c5e64778805ab2f TO IDX_UA_ANNONCE
        SQL);
    }
}
