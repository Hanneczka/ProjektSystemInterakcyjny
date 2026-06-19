<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260619115540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, slug VARCHAR(64) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, element_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_9474526C1F1F2A24 (element_id), INDEX IDX_9474526CF675F31B (author_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cover (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(191) NOT NULL, element_id INT NOT NULL, UNIQUE INDEX UNIQ_8D0886C51F1F2A24 (element_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE element (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, title VARCHAR(80) NOT NULL, author VARCHAR(80) DEFAULT NULL, year INT DEFAULT NULL, category_id INT NOT NULL, INDEX IDX_41405E3912469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE element_tag (element_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_544124EB1F1F2A24 (element_id), INDEX IDX_544124EBBAD26311 (tag_id), PRIMARY KEY (element_id, tag_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rating (id INT AUTO_INCREMENT NOT NULL, value INT NOT NULL, element_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D88926221F1F2A24 (element_id), INDEX IDX_D8892622A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, slug VARCHAR(64) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(64) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_element (user_id INT NOT NULL, element_id INT NOT NULL, INDEX IDX_194D4033A76ED395 (user_id), INDEX IDX_194D40331F1F2A24 (element_id), PRIMARY KEY (user_id, element_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C1F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cover ADD CONSTRAINT FK_8D0886C51F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E3912469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE element_tag ADD CONSTRAINT FK_544124EB1F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE element_tag ADD CONSTRAINT FK_544124EBBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D88926221F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_element ADD CONSTRAINT FK_194D4033A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_element ADD CONSTRAINT FK_194D40331F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C1F1F2A24');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE cover DROP FOREIGN KEY FK_8D0886C51F1F2A24');
        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E3912469DE2');
        $this->addSql('ALTER TABLE element_tag DROP FOREIGN KEY FK_544124EB1F1F2A24');
        $this->addSql('ALTER TABLE element_tag DROP FOREIGN KEY FK_544124EBBAD26311');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D88926221F1F2A24');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622A76ED395');
        $this->addSql('ALTER TABLE user_element DROP FOREIGN KEY FK_194D4033A76ED395');
        $this->addSql('ALTER TABLE user_element DROP FOREIGN KEY FK_194D40331F1F2A24');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE cover');
        $this->addSql('DROP TABLE element');
        $this->addSql('DROP TABLE element_tag');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_element');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
