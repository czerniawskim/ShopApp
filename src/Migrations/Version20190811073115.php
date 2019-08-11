<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190811073115 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE categories (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE categories_tags (categories_id INTEGER NOT NULL, tags_id INTEGER NOT NULL, PRIMARY KEY(categories_id, tags_id))');
        $this->addSql('CREATE INDEX IDX_B78FCCA0A21214B7 ON categories_tags (categories_id)');
        $this->addSql('CREATE INDEX IDX_B78FCCA08D7B4FB4 ON categories_tags (tags_id)');
        $this->addSql('CREATE TABLE tags (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('DROP INDEX IDX_EF39849B9395C3F3');
        $this->addSql('CREATE TEMPORARY TABLE __temp__deals AS SELECT id, customer_id, prods, done_at FROM deals');
        $this->addSql('DROP TABLE deals');
        $this->addSql('CREATE TABLE deals (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, customer_id INTEGER NOT NULL, prods CLOB NOT NULL COLLATE BINARY --(DC2Type:json_array)
        , done_at DATETIME NOT NULL, CONSTRAINT FK_EF39849B9395C3F3 FOREIGN KEY (customer_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO deals (id, customer_id, prods, done_at) SELECT id, customer_id, prods, done_at FROM __temp__deals');
        $this->addSql('DROP TABLE __temp__deals');
        $this->addSql('CREATE INDEX IDX_EF39849B9395C3F3 ON deals (customer_id)');
        $this->addSql('DROP INDEX UNIQ_1483A5E9BB1A0722');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, details_id, username, password, email, reset_pass FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, details_id INTEGER NOT NULL, username VARCHAR(30) NOT NULL COLLATE BINARY, password VARCHAR(20) NOT NULL COLLATE BINARY, email VARCHAR(255) NOT NULL COLLATE BINARY, reset_pass VARCHAR(40) NOT NULL COLLATE BINARY, CONSTRAINT FK_1483A5E9BB1A0722 FOREIGN KEY (details_id) REFERENCES user_details (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO users (id, details_id, username, password, email, reset_pass) SELECT id, details_id, username, password, email, reset_pass FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9BB1A0722 ON users (details_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE categories_tags');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP INDEX IDX_EF39849B9395C3F3');
        $this->addSql('CREATE TEMPORARY TABLE __temp__deals AS SELECT id, customer_id, prods, done_at FROM deals');
        $this->addSql('DROP TABLE deals');
        $this->addSql('CREATE TABLE deals (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, customer_id INTEGER NOT NULL, prods CLOB NOT NULL --(DC2Type:json_array)
        , done_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO deals (id, customer_id, prods, done_at) SELECT id, customer_id, prods, done_at FROM __temp__deals');
        $this->addSql('DROP TABLE __temp__deals');
        $this->addSql('CREATE INDEX IDX_EF39849B9395C3F3 ON deals (customer_id)');
        $this->addSql('DROP INDEX UNIQ_1483A5E9BB1A0722');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, details_id, username, password, email, reset_pass FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, details_id INTEGER NOT NULL, username VARCHAR(30) NOT NULL, password VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, reset_pass VARCHAR(40) NOT NULL)');
        $this->addSql('INSERT INTO users (id, details_id, username, password, email, reset_pass) SELECT id, details_id, username, password, email, reset_pass FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9BB1A0722 ON users (details_id)');
    }
}
