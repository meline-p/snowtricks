<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240405094506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD category_order INT NOT NULL');
        $this->addSql('ALTER TABLE user_trick CHANGE operation operation ENUM(\'create\', \'update\', \'delete\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP category_order');
        $this->addSql('ALTER TABLE user_trick CHANGE operation operation VARCHAR(255) DEFAULT NULL');
    }
}
