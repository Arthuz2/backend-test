<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260222192515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE investment (id BINARY(16) NOT NULL, created_at DATETIME NOT NULL, invested_amount NUMERIC(15, 2) NOT NULL, withdraw_at DATETIME DEFAULT NULL, owner_id BINARY(16) NOT NULL, INDEX IDX_43CA0AD67E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE owner (id BINARY(16) NOT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_CF60E67CE7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE investment ADD CONSTRAINT FK_43CA0AD67E3C61F9 FOREIGN KEY (owner_id) REFERENCES owner (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE investment DROP FOREIGN KEY FK_43CA0AD67E3C61F9');
        $this->addSql('DROP TABLE investment');
        $this->addSql('DROP TABLE owner');
    }
}
