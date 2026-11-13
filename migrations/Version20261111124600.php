<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261111124600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE messages CHANGE name name VARCHAR(120) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE subject subject VARCHAR(200) NOT NULL, CHANGE message message LONGTEXT NOT NULL, CHANGE is_read is_read TINYINT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE order_items CHANGE product_name product_name VARCHAR(160) NOT NULL, CHANGE price price NUMERIC(10, 2) NOT NULL, CHANGE quantity quantity INT NOT NULL, CHANGE total total NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB08D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB04584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_62809DB08D9F6D38 ON order_items (order_id)');
        $this->addSql('CREATE INDEX IDX_62809DB04584665A ON order_items (product_id)');
        $this->addSql('ALTER TABLE orders ADD address VARCHAR(255) NOT NULL, ADD city VARCHAR(100) NOT NULL, ADD postal VARCHAR(30) NOT NULL, ADD country VARCHAR(40) NOT NULL, ADD payment_method VARCHAR(40) NOT NULL, ADD transaction_id VARCHAR(64) DEFAULT NULL, CHANGE first_name first_name VARCHAR(80) NOT NULL, CHANGE last_name last_name VARCHAR(80) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE total total NUMERIC(10, 2) NOT NULL, CHANGE status status VARCHAR(30) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_E52FFDEEA76ED395 ON orders (user_id)');
        $this->addSql('ALTER TABLE products ADD sku VARCHAR(64) NOT NULL, ADD color VARCHAR(80) DEFAULT NULL, ADD category VARCHAR(80) NOT NULL, ADD gender VARCHAR(20) NOT NULL, ADD badge VARCHAR(40) DEFAULT NULL, ADD bg VARCHAR(30) NOT NULL, ADD image VARCHAR(255) DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD updated_at DATETIME NOT NULL, ADD created_by_id INT NOT NULL, CHANGE name name VARCHAR(160) NOT NULL, CHANGE quantity quantity INT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PRODUCT_SKU ON products (sku)');
        $this->addSql('CREATE INDEX IDX_B3BA5A5AB03A8386 ON products (created_by_id)');
        $this->addSql('ALTER TABLE users ADD name VARCHAR(120) NOT NULL, ADD roles JSON NOT NULL, ADD status VARCHAR(20) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE first_name first_name VARCHAR(80) NOT NULL, CHANGE last_name last_name VARCHAR(80) NOT NULL, CHANGE role role VARCHAR(20) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE users RENAME INDEX email TO UNIQ_USERS_EMAIL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE messages CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE subject subject VARCHAR(255) DEFAULT NULL, CHANGE message message TEXT DEFAULT NULL, CHANGE is_read is_read TINYINT DEFAULT 0, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB08D9F6D38');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB04584665A');
        $this->addSql('DROP INDEX IDX_62809DB08D9F6D38 ON order_items');
        $this->addSql('DROP INDEX IDX_62809DB04584665A ON order_items');
        $this->addSql('ALTER TABLE order_items CHANGE product_name product_name VARCHAR(255) DEFAULT NULL, CHANGE price price NUMERIC(10, 2) DEFAULT NULL, CHANGE quantity quantity INT DEFAULT NULL, CHANGE total total NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEA76ED395');
        $this->addSql('DROP INDEX IDX_E52FFDEEA76ED395 ON orders');
        $this->addSql('ALTER TABLE orders DROP address, DROP city, DROP postal, DROP country, DROP payment_method, DROP transaction_id, CHANGE first_name first_name VARCHAR(100) DEFAULT NULL, CHANGE last_name last_name VARCHAR(100) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE total total NUMERIC(10, 2) DEFAULT NULL, CHANGE status status VARCHAR(20) DEFAULT \'pending\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5AB03A8386');
        $this->addSql('DROP INDEX UNIQ_PRODUCT_SKU ON products');
        $this->addSql('DROP INDEX IDX_B3BA5A5AB03A8386 ON products');
        $this->addSql('ALTER TABLE products DROP sku, DROP color, DROP category, DROP gender, DROP badge, DROP bg, DROP image, DROP description, DROP updated_at, DROP created_by_id, CHANGE name name VARCHAR(255) NOT NULL, CHANGE quantity quantity INT DEFAULT 0, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE users DROP name, DROP roles, DROP status, CHANGE first_name first_name VARCHAR(100) DEFAULT NULL, CHANGE last_name last_name VARCHAR(100) DEFAULT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE role role VARCHAR(20) DEFAULT \'user\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE users RENAME INDEX uniq_users_email TO email');
    }
}
