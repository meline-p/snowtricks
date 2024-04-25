<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425202608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_trick ADD id INT AUTO_INCREMENT NOT NULL, CHANGE operation operation ENUM(\'create\', \'update\', \'delete\'), DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_trick MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON user_trick');
        $this->addSql('ALTER TABLE user_trick DROP id, CHANGE operation operation VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_trick ADD PRIMARY KEY (user_id, trick_id)');
    }
}
