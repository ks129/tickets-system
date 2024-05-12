<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240512175445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE event_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE event_news_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE event_ticket_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE event (id INT NOT NULL, name VARCHAR(255) NOT NULL, begin_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN event.begin_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN event.end_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE event_user_host (event_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(event_id, user_id))');
        $this->addSql('CREATE INDEX IDX_3333610971F7E88B ON event_user_host (event_id)');
        $this->addSql('CREATE INDEX IDX_33336109A76ED395 ON event_user_host (user_id)');
        $this->addSql('CREATE TABLE event_user_checker (event_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(event_id, user_id))');
        $this->addSql('CREATE INDEX IDX_ECC91B2B71F7E88B ON event_user_checker (event_id)');
        $this->addSql('CREATE INDEX IDX_ECC91B2BA76ED395 ON event_user_checker (user_id)');
        $this->addSql('CREATE TABLE event_news (id INT NOT NULL, event_id INT NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_218D5FB71F7E88B ON event_news (event_id)');
        $this->addSql('COMMENT ON COLUMN event_news.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN event_news.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE event_ticket_type (id INT NOT NULL, event_id INT NOT NULL, name VARCHAR(255) NOT NULL, price NUMERIC(10, 3) NOT NULL, requirements TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_158C919171F7E88B ON event_ticket_type (event_id)');
        $this->addSql('ALTER TABLE event_user_host ADD CONSTRAINT FK_3333610971F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event_user_host ADD CONSTRAINT FK_33336109A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event_user_checker ADD CONSTRAINT FK_ECC91B2B71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event_user_checker ADD CONSTRAINT FK_ECC91B2BA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event_news ADD CONSTRAINT FK_218D5FB71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event_ticket_type ADD CONSTRAINT FK_158C919171F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE event_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE event_news_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE event_ticket_type_id_seq CASCADE');
        $this->addSql('ALTER TABLE event_user_host DROP CONSTRAINT FK_3333610971F7E88B');
        $this->addSql('ALTER TABLE event_user_host DROP CONSTRAINT FK_33336109A76ED395');
        $this->addSql('ALTER TABLE event_user_checker DROP CONSTRAINT FK_ECC91B2B71F7E88B');
        $this->addSql('ALTER TABLE event_user_checker DROP CONSTRAINT FK_ECC91B2BA76ED395');
        $this->addSql('ALTER TABLE event_news DROP CONSTRAINT FK_218D5FB71F7E88B');
        $this->addSql('ALTER TABLE event_ticket_type DROP CONSTRAINT FK_158C919171F7E88B');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_user_host');
        $this->addSql('DROP TABLE event_user_checker');
        $this->addSql('DROP TABLE event_news');
        $this->addSql('DROP TABLE event_ticket_type');
    }
}
