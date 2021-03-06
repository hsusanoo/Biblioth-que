<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190423112358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE livre ADD added_by_id int DEFAULT NULL, ADD editeur varchar(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE livre ADD CONSTRAINT FK_AC634F9955B127A4 FOREIGN KEY (added_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AC634F9955B127A4 ON livre (added_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE livre DROP FOREIGN KEY FK_AC634F9955B127A4');
        $this->addSql('DROP INDEX IDX_AC634F9955B127A4 ON livre');
        $this->addSql('ALTER TABLE livre DROP added_by_id, DROP editeur');
    }
}
