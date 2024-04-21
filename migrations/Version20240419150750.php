<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419150750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP promote_image');
        $this->addSql('ALTER TABLE trick ADD promote_image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91ED80E7B11 FOREIGN KEY (promote_image_id) REFERENCES image (id)');
        $this->addSql('CREATE INDEX IDX_D8F0A91ED80E7B11 ON trick (promote_image_id)');
        $this->addSql('ALTER TABLE user_trick CHANGE operation operation ENUM(\'create\', \'update\', \'delete\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image ADD promote_image TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91ED80E7B11');
        $this->addSql('DROP INDEX IDX_D8F0A91ED80E7B11 ON trick');
        $this->addSql('ALTER TABLE trick DROP promote_image_id');
        $this->addSql('ALTER TABLE user_trick CHANGE operation operation VARCHAR(255) DEFAULT NULL');
    }
}
