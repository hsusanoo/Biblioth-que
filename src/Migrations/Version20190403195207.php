<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190403195207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE descripteur (id int AUTO_INCREMENT NOT NULL, nom varchar(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE descripteur_livre (descripteur_id int NOT NULL, livre_id int NOT NULL, INDEX IDX_1D7EBB364AC23114 (descripteur_id), INDEX IDX_1D7EBB3637D925CB (livre_id), PRIMARY KEY(descripteur_id, livre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE descripteur_livre ADD CONSTRAINT FK_1D7EBB364AC23114 FOREIGN KEY (descripteur_id) REFERENCES descripteur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE descripteur_livre ADD CONSTRAINT FK_1D7EBB3637D925CB FOREIGN KEY (livre_id) REFERENCES livre (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE descripteur_livre DROP FOREIGN KEY FK_1D7EBB364AC23114');
        $this->addSql('DROP TABLE descripteur');
        $this->addSql('DROP TABLE descripteur_livre');
    }
}
