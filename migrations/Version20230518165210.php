<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230518165210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CC5967ED8');
        $this->addSql('DROP INDEX IDX_FDCA8C9CC5967ED8 ON cours');
        $this->addSql('ALTER TABLE cours CHANGE ue_id u_e_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CC5967ED8 FOREIGN KEY (u_e_id) REFERENCES ue (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9CC5967ED8 ON cours (u_e_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CC5967ED8');
        $this->addSql('DROP INDEX IDX_FDCA8C9CC5967ED8 ON cours');
        $this->addSql('ALTER TABLE cours CHANGE u_e_id ue_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CC5967ED8 FOREIGN KEY (ue_id) REFERENCES ue (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_FDCA8C9CC5967ED8 ON cours (ue_id)');
    }
}
