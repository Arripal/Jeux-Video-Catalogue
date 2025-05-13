<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423163213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE game (id SERIAL NOT NULL, api_id INT NOT NULL, title VARCHAR(255) NOT NULL, release_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, publisher VARCHAR(255) DEFAULT NULL, genres JSON NOT NULL, plateforms JSON NOT NULL, franchise VARCHAR(255) DEFAULT NULL, developers VARCHAR(255) DEFAULT NULL, global_rating INT DEFAULT NULL, rating_count INT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN game.release_date IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE player (id SERIAL NOT NULL, profile_user_id INT NOT NULL, bio TEXT DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_98197A6574D00D09 ON player (profile_user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE recap (id SERIAL NOT NULL, player_id INT NOT NULL, game_id INT NOT NULL, status VARCHAR(255) DEFAULT NULL, rating INT DEFAULT NULL, playtime INT DEFAULT NULL, added_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, last_updated TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2FBA1D2099E6F5DF ON recap (player_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2FBA1D20E48FD905 ON recap (game_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN recap.added_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN recap.last_updated IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE player ADD CONSTRAINT FK_98197A6574D00D09 FOREIGN KEY (profile_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recap ADD CONSTRAINT FK_2FBA1D2099E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recap ADD CONSTRAINT FK_2FBA1D20E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE player DROP CONSTRAINT FK_98197A6574D00D09
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recap DROP CONSTRAINT FK_2FBA1D2099E6F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recap DROP CONSTRAINT FK_2FBA1D20E48FD905
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE game
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE player
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE recap
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
    }
}
