<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210520223035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_event_store (event_id VARCHAR(26) NOT NULL, event_type VARCHAR(255) NOT NULL, aggregate_root_id VARCHAR(36) DEFAULT NULL, aggregate_root_version SMALLINT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, payload TEXT NOT NULL, metadata TEXT DEFAULT NULL, PRIMARY KEY(event_id))');
        $this->addSql('CREATE INDEX idx_01F65S09T9MJZS3ZQNDJ5X0F9J ON user_event_store (aggregate_root_id) WHERE (aggregate_root_id IS NOT NULL)');
        $this->addSql('COMMENT ON COLUMN user_event_store.payload IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN user_event_store.metadata IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_event_store');
    }
}
