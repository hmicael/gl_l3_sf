<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230515153209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE holiday (id INT AUTO_INCREMENT NOT NULL, general_constraints_id INT NOT NULL, type VARCHAR(15) NOT NULL, beginning DATE NOT NULL, end DATE DEFAULT NULL, INDEX IDX_DC9AB234FC7E1598 (general_constraints_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE holiday ADD CONSTRAINT FK_DC9AB234FC7E1598 FOREIGN KEY (general_constraints_id) REFERENCES general_constraints (id)');
        $this->addSql('ALTER TABLE general_constraints DROP holiday, DROP exams, CHANGE course_max_duration course_max_duration TIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE holiday DROP FOREIGN KEY FK_DC9AB234FC7E1598');
        $this->addSql('DROP TABLE holiday');
        $this->addSql('ALTER TABLE general_constraints ADD holiday VARCHAR(255) NOT NULL, ADD exams VARCHAR(255) NOT NULL, CHANGE course_max_duration course_max_duration INT NOT NULL');
    }
}
