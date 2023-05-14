<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230514142103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, ue_id INT DEFAULT NULL, name VARCHAR(15) NOT NULL, type VARCHAR(10) NOT NULL, duration INT NOT NULL, position INT NOT NULL, INDEX IDX_FDCA8C9CC5967ED8 (ue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, semester INT NOT NULL, group_id VARCHAR(15) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(10) NOT NULL, capacity INT NOT NULL, type VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE slot (id INT AUTO_INCREMENT NOT NULL, room_id INT DEFAULT NULL, cours_id INT DEFAULT NULL, planning_id INT DEFAULT NULL, start_hour TIME NOT NULL, end_hour TIME NOT NULL, date DATE NOT NULL, INDEX IDX_AC0E206754177093 (room_id), INDEX IDX_AC0E20677ECF78B0 (cours_id), INDEX IDX_AC0E20673D865311 (planning_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ue (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(15) NOT NULL, start_date DATE DEFAULT NULL, nb_student INT NOT NULL, nb_group INT NOT NULL, constraints_applied TINYINT(1) NOT NULL, semester INT NOT NULL, filiere LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CC5967ED8 FOREIGN KEY (ue_id) REFERENCES ue (id)');
        $this->addSql('ALTER TABLE slot ADD CONSTRAINT FK_AC0E206754177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE slot ADD CONSTRAINT FK_AC0E20677ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE slot ADD CONSTRAINT FK_AC0E20673D865311 FOREIGN KEY (planning_id) REFERENCES planning (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CC5967ED8');
        $this->addSql('ALTER TABLE slot DROP FOREIGN KEY FK_AC0E206754177093');
        $this->addSql('ALTER TABLE slot DROP FOREIGN KEY FK_AC0E20677ECF78B0');
        $this->addSql('ALTER TABLE slot DROP FOREIGN KEY FK_AC0E20673D865311');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE slot');
        $this->addSql('DROP TABLE ue');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
