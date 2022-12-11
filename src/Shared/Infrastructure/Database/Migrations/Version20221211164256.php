<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221211164256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, weight INT NOT NULL, category VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE parsing_settings DROP FOREIGN KEY parsing_settings_ibfk_1');
        $this->addSql('ALTER TABLE pay_types_exchanges_codes DROP FOREIGN KEY pay_types_exchanges_codes_ibfk_1');
        $this->addSql('ALTER TABLE pay_types_exchanges_codes DROP FOREIGN KEY pay_types_exchanges_codes_ibfk_2');
        $this->addSql('ALTER TABLE tickers_exchanges_settings DROP FOREIGN KEY tickers_exchanges_settings_ibfk_1');
        $this->addSql('ALTER TABLE tickers_exchanges_settings DROP FOREIGN KEY tickers_exchanges_settings_ibfk_2');
        $this->addSql('DROP TABLE exchanges');
        $this->addSql('DROP TABLE parsing_settings');
        $this->addSql('DROP TABLE pay_types');
        $this->addSql('DROP TABLE pay_types_exchanges_codes');
        $this->addSql('DROP TABLE telegram_user');
        $this->addSql('DROP TABLE tickers');
        $this->addSql('DROP TABLE tickers_exchanges_settings');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE video_data');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE exchanges (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE parsing_settings (id INT AUTO_INCREMENT NOT NULL, exchange_id INT NOT NULL, user_id INT NOT NULL, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, volume_buy_min DOUBLE PRECISION NOT NULL, volume_buy_max DOUBLE PRECISION NOT NULL, volume_sell_min DOUBLE PRECISION NOT NULL, volume_sell_max DOUBLE PRECISION NOT NULL, trade_type VARCHAR(255) CHARACTER SET utf8 DEFAULT \'buy\' NOT NULL COLLATE `utf8_general_ci`, INDEX exchange_id (exchange_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE pay_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE pay_types_exchanges_codes (id INT AUTO_INCREMENT NOT NULL, pay_type_id INT NOT NULL, exchange_id INT NOT NULL, code VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, INDEX exchange_id (exchange_id), INDEX pay_type_id (pay_type_id), INDEX code (exchange_id, code, pay_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE telegram_user (id INT AUTO_INCREMENT NOT NULL, telegram_id BIGINT NOT NULL, first_name VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, last_name VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, username VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, photo_url TEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, auth_date BIGINT DEFAULT NULL, hash VARCHAR(64) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, UNIQUE INDEX hash (hash), UNIQUE INDEX id (telegram_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tickers (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(4) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, is_asset TINYINT(1) NOT NULL, is_fiat TINYINT(1) NOT NULL, UNIQUE INDEX code (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tickers_exchanges_settings (id INT AUTO_INCREMENT NOT NULL, ticker_id INT NOT NULL, exchange_id INT NOT NULL, is_asset TINYINT(1) NOT NULL, is_fiat TINYINT(1) NOT NULL, INDEX ticker_id (ticker_id), INDEX exchange_id (exchange_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, telegram_id INT NOT NULL, username INT NOT NULL, access_token VARCHAR(64) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, UNIQUE INDEX username (username), UNIQUE INDEX access_token (access_token), UNIQUE INDEX telegram_id (telegram_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE video_data (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, file_id VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, tags TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, UNIQUE INDEX file_id (file_id), UNIQUE INDEX url (url), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE parsing_settings ADD CONSTRAINT parsing_settings_ibfk_1 FOREIGN KEY (exchange_id) REFERENCES exchanges (id)');
        $this->addSql('ALTER TABLE pay_types_exchanges_codes ADD CONSTRAINT pay_types_exchanges_codes_ibfk_1 FOREIGN KEY (exchange_id) REFERENCES exchanges (id)');
        $this->addSql('ALTER TABLE pay_types_exchanges_codes ADD CONSTRAINT pay_types_exchanges_codes_ibfk_2 FOREIGN KEY (pay_type_id) REFERENCES pay_types (id)');
        $this->addSql('ALTER TABLE tickers_exchanges_settings ADD CONSTRAINT tickers_exchanges_settings_ibfk_1 FOREIGN KEY (ticker_id) REFERENCES tickers (id)');
        $this->addSql('ALTER TABLE tickers_exchanges_settings ADD CONSTRAINT tickers_exchanges_settings_ibfk_2 FOREIGN KEY (exchange_id) REFERENCES exchanges (id)');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
