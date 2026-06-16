<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260616142705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cover (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(191) NOT NULL, element_id INT NOT NULL, UNIQUE INDEX UNIQ_8D0886C51F1F2A24 (element_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE cover ADD CONSTRAINT FK_8D0886C51F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cover DROP FOREIGN KEY FK_8D0886C51F1F2A24');
        $this->addSql('DROP TABLE cover');
    }
}
