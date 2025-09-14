<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250911205613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depense ADD CONSTRAINT FK_34059757C54C8C93 FOREIGN KEY (type_id) REFERENCES type_depense (id)');
        $this->addSql('CREATE INDEX IDX_34059757C54C8C93 ON depense (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depense DROP FOREIGN KEY FK_34059757C54C8C93');
        $this->addSql('DROP TABLE type_depense');
        $this->addSql('DROP INDEX IDX_34059757C54C8C93 ON depense');
        $this->addSql('ALTER TABLE depense DROP detail, DROP date');
    }
}
