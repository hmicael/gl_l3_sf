<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230515213535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours CHANGE duration duration TIME NOT NULL');
        $this->addSql('ALTER TABLE ue CHANGE nb_cours nb_cours INT NOT NULL, CHANGE nb_td nb_td INT NOT NULL, CHANGE nb_tp nb_tp INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours CHANGE duration duration INT NOT NULL');
        $this->addSql('ALTER TABLE ue CHANGE nb_cours nb_cours INT DEFAULT NULL, CHANGE nb_td nb_td INT DEFAULT NULL, CHANGE nb_tp nb_tp INT DEFAULT NULL');
    }
}
