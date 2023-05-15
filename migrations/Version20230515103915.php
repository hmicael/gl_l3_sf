<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230515103915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ue CHANGE constraints_applied constraints_applied TINYINT(1) DEFAULT NULL, CHANGE filiere filiere VARCHAR(100) NOT NULL, CHANGE teacher teacher VARCHAR(30) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ue CHANGE constraints_applied constraints_applied TINYINT(1) NOT NULL, CHANGE filiere filiere LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', CHANGE teacher teacher VARCHAR(30) NOT NULL');
    }
}
