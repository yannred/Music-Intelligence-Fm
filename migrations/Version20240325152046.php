<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240325152046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE loved_track (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, track_id INT NOT NULL, INDEX IDX_98F72301A76ED395 (user_id), INDEX IDX_98F723015ED23C43 (track_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE loved_track ADD CONSTRAINT FK_98F72301A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE loved_track ADD CONSTRAINT FK_98F723015ED23C43 FOREIGN KEY (track_id) REFERENCES track (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loved_track DROP FOREIGN KEY FK_98F72301A76ED395');
        $this->addSql('ALTER TABLE loved_track DROP FOREIGN KEY FK_98F723015ED23C43');
        $this->addSql('DROP TABLE loved_track');
    }
}
