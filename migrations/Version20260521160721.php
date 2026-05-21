<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260521160721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE episode_downloads_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE episode_downloads (episode_id UUID NOT NULL, podcast_id UUID NOT NULL, occurred_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX idx_episode_downloads_lookup ON episode_downloads (podcast_id, episode_id, occurred_at)');
        $this->addSql('CREATE TABLE incoming_events (type VARCHAR(255) NOT NULL, occurred_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, data JSON NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, id UUID NOT NULL, PRIMARY KEY (id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE episode_downloads_id_seq CASCADE');
        $this->addSql('DROP TABLE episode_downloads');
        $this->addSql('DROP TABLE incoming_events');
    }
}
