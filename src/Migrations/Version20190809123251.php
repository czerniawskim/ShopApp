<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190809123251 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE deals (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, customer_id INTEGER NOT NULL, prods CLOB NOT NULL --(DC2Type:json_array)
        , done_at DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_EF39849B9395C3F3 ON deals (customer_id)');
        $this->addSql('CREATE TABLE products (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, description CLOB DEFAULT NULL, image BLOB NOT NULL)');
        $this->addSql('CREATE TABLE user_details (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, city VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, join_date DATETIME NOT NULL, phone VARCHAR(15) DEFAULT NULL, zip VARCHAR(10) DEFAULT NULL)');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(30) NOT NULL, password VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, reset_pass VARCHAR(25) NOT NULL)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE deals');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE user_details');
        $this->addSql('DROP TABLE users');
    }
}
