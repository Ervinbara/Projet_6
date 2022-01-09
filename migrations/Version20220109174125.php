<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220109174125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A6D69186E');
        $this->addSql('DROP INDEX IDX_E01FBE6A6D69186E ON images');
        $this->addSql('ALTER TABLE images CHANGE figure_id_id figure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
        $this->addSql('CREATE INDEX IDX_E01FBE6A5C011B5 ON images (figure_id)');
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA64326D69186E');
        $this->addSql('DROP INDEX IDX_29AA64326D69186E ON videos');
        $this->addSql('ALTER TABLE videos CHANGE figure_id_id figure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA64325C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
        $this->addSql('CREATE INDEX IDX_29AA64325C011B5 ON videos (figure_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A5C011B5');
        $this->addSql('DROP INDEX IDX_E01FBE6A5C011B5 ON images');
        $this->addSql('ALTER TABLE images CHANGE figure_id figure_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A6D69186E FOREIGN KEY (figure_id_id) REFERENCES figure (id)');
        $this->addSql('CREATE INDEX IDX_E01FBE6A6D69186E ON images (figure_id_id)');
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA64325C011B5');
        $this->addSql('DROP INDEX IDX_29AA64325C011B5 ON videos');
        $this->addSql('ALTER TABLE videos CHANGE figure_id figure_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA64326D69186E FOREIGN KEY (figure_id_id) REFERENCES figure (id)');
        $this->addSql('CREATE INDEX IDX_29AA64326D69186E ON videos (figure_id_id)');
    }
}
