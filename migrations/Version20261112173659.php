<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261112173659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, email VARCHAR(180) NOT NULL, subject VARCHAR(200) NOT NULL, message LONGTEXT NOT NULL, is_read TINYINT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_items (id INT AUTO_INCREMENT NOT NULL, product_name VARCHAR(160) NOT NULL, price NUMERIC(10, 2) NOT NULL, quantity INT NOT NULL, total NUMERIC(10, 2) NOT NULL, order_id INT NOT NULL, product_id INT DEFAULT NULL, INDEX IDX_62809DB08D9F6D38 (order_id), INDEX IDX_62809DB04584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(80) NOT NULL, last_name VARCHAR(80) NOT NULL, email VARCHAR(180) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(100) NOT NULL, postal VARCHAR(30) NOT NULL, country VARCHAR(40) NOT NULL, payment_method VARCHAR(40) NOT NULL, total NUMERIC(10, 2) NOT NULL, status VARCHAR(30) NOT NULL, transaction_id VARCHAR(64) DEFAULT NULL, created_at DATETIME NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_E52FFDEEA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(160) NOT NULL, sku VARCHAR(64) NOT NULL, color VARCHAR(80) DEFAULT NULL, category VARCHAR(80) NOT NULL, gender VARCHAR(20) NOT NULL, badge VARCHAR(40) DEFAULT NULL, bg VARCHAR(30) NOT NULL, image VARCHAR(255) DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, quantity INT NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by_id INT NOT NULL, UNIQUE INDEX UNIQ_PRODUCT_SKU (sku), INDEX IDX_B3BA5A5AB03A8386 (created_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(80) NOT NULL, last_name VARCHAR(80) NOT NULL, name VARCHAR(120) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, role VARCHAR(20) NOT NULL, password_hash VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_USERS_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB08D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB04584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB08D9F6D38');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB04584665A');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEA76ED395');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5AB03A8386');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
