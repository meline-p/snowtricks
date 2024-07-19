<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240705145241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP category_order');
        $this->addSql('DROP INDEX uniq_identifier_name ON trick');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8F0A91E5E237E06 ON trick (name)');
        $this->addSql('ALTER TABLE user_trick CHANGE operation operation ENUM(\'create\', \'update\', \'delete\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD category_order INT DEFAULT NULL');
        $this->addSql('DROP INDEX uniq_d8f0a91e5e237e06 ON trick');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_NAME ON trick (name)');
        $this->addSql('ALTER TABLE user_trick CHANGE operation operation VARCHAR(255) DEFAULT NULL');
    }
}
