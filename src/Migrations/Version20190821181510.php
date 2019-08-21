<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190821181510 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_B78FCCA08D7B4FB4');
        $this->addSql('DROP INDEX IDX_B78FCCA0A21214B7');
        $this->addSql('CREATE TEMPORARY TABLE __temp__categories_tags AS SELECT categories_id, tags_id FROM categories_tags');
        $this->addSql('DROP TABLE categories_tags');
        $this->addSql('CREATE TABLE categories_tags (categories_id INTEGER NOT NULL, tags_id INTEGER NOT NULL, PRIMARY KEY(categories_id, tags_id), CONSTRAINT FK_B78FCCA0A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B78FCCA08D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO categories_tags (categories_id, tags_id) SELECT categories_id, tags_id FROM __temp__categories_tags');
        $this->addSql('DROP TABLE __temp__categories_tags');
        $this->addSql('CREATE INDEX IDX_B78FCCA08D7B4FB4 ON categories_tags (tags_id)');
        $this->addSql('CREATE INDEX IDX_B78FCCA0A21214B7 ON categories_tags (categories_id)');
        $this->addSql('DROP INDEX IDX_EF39849B9395C3F3');
        $this->addSql('CREATE TEMPORARY TABLE __temp__deals AS SELECT id, customer_id, done_at, prods FROM deals');
        $this->addSql('DROP TABLE deals');
        $this->addSql('CREATE TABLE deals (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, customer_id INTEGER NOT NULL, done_at DATETIME NOT NULL, prods CLOB NOT NULL COLLATE BINARY --(DC2Type:array)
        , CONSTRAINT FK_EF39849B9395C3F3 FOREIGN KEY (customer_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO deals (id, customer_id, done_at, prods) SELECT id, customer_id, done_at, prods FROM __temp__deals');
        $this->addSql('DROP TABLE __temp__deals');
        $this->addSql('CREATE INDEX IDX_EF39849B9395C3F3 ON deals (customer_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__products AS SELECT id, name, price, description, image, gallery_link FROM products');
        $this->addSql('DROP TABLE products');
        $this->addSql('CREATE TABLE products (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, price DOUBLE PRECISION NOT NULL, description CLOB DEFAULT NULL COLLATE BINARY, image BLOB NOT NULL, gallery_link VARCHAR(750) DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO products (id, name, price, description, image, gallery_link) SELECT id, name, price, description, image, gallery_link FROM __temp__products');
        $this->addSql('DROP TABLE __temp__products');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A12469DE2 ON products (category_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_B78FCCA0A21214B7');
        $this->addSql('DROP INDEX IDX_B78FCCA08D7B4FB4');
        $this->addSql('CREATE TEMPORARY TABLE __temp__categories_tags AS SELECT categories_id, tags_id FROM categories_tags');
        $this->addSql('DROP TABLE categories_tags');
        $this->addSql('CREATE TABLE categories_tags (categories_id INTEGER NOT NULL, tags_id INTEGER NOT NULL, PRIMARY KEY(categories_id, tags_id))');
        $this->addSql('INSERT INTO categories_tags (categories_id, tags_id) SELECT categories_id, tags_id FROM __temp__categories_tags');
        $this->addSql('DROP TABLE __temp__categories_tags');
        $this->addSql('CREATE INDEX IDX_B78FCCA0A21214B7 ON categories_tags (categories_id)');
        $this->addSql('CREATE INDEX IDX_B78FCCA08D7B4FB4 ON categories_tags (tags_id)');
        $this->addSql('DROP INDEX IDX_EF39849B9395C3F3');
        $this->addSql('CREATE TEMPORARY TABLE __temp__deals AS SELECT id, customer_id, prods, done_at FROM deals');
        $this->addSql('DROP TABLE deals');
        $this->addSql('CREATE TABLE deals (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, customer_id INTEGER NOT NULL, prods CLOB NOT NULL --(DC2Type:array)
        , done_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO deals (id, customer_id, prods, done_at) SELECT id, customer_id, prods, done_at FROM __temp__deals');
        $this->addSql('DROP TABLE __temp__deals');
        $this->addSql('CREATE INDEX IDX_EF39849B9395C3F3 ON deals (customer_id)');
        $this->addSql('DROP INDEX IDX_B3BA5A5A12469DE2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__products AS SELECT id, name, price, description, image, gallery_link FROM products');
        $this->addSql('DROP TABLE products');
        $this->addSql('CREATE TABLE products (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, description CLOB DEFAULT NULL, image BLOB NOT NULL, gallery_link VARCHAR(750) DEFAULT NULL)');
        $this->addSql('INSERT INTO products (id, name, price, description, image, gallery_link) SELECT id, name, price, description, image, gallery_link FROM __temp__products');
        $this->addSql('DROP TABLE __temp__products');
    }
}
