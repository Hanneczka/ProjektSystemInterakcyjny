<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260525102133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE element_tag (element_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_544124EB1F1F2A24 (element_id), INDEX IDX_544124EBBAD26311 (tag_id), PRIMARY KEY (element_id, tag_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE element_tag ADD CONSTRAINT FK_544124EB1F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE element_tag ADD CONSTRAINT FK_544124EBBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element_tag DROP FOREIGN KEY FK_544124EB1F1F2A24');
        $this->addSql('ALTER TABLE element_tag DROP FOREIGN KEY FK_544124EBBAD26311');
        $this->addSql('DROP TABLE element_tag');
    }
}
