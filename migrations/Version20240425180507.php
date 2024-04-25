<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425180507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP INDEX IDX_D8F0A91ED80E7B11, ADD UNIQUE INDEX UNIQ_D8F0A91ED80E7B11 (promote_image_id)');
        $this->addSql('ALTER TABLE user_trick CHANGE operation operation ENUM(\'create\', \'update\', \'delete\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP INDEX UNIQ_D8F0A91ED80E7B11, ADD INDEX IDX_D8F0A91ED80E7B11 (promote_image_id)');
        $this->addSql('ALTER TABLE user_trick CHANGE operation operation VARCHAR(255) DEFAULT NULL');
    }
}
