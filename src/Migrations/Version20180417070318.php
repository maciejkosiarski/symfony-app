<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180417070318 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE app_exercises RENAME COLUMN type TO type_id');
        $this->addSql('ALTER TABLE app_exercises ADD CONSTRAINT FK_A7CE16A4C54C8C93 FOREIGN KEY (type_id) REFERENCES app_exercise_types (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A7CE16A4C54C8C93 ON app_exercises (type_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE app_exercises DROP CONSTRAINT FK_A7CE16A4C54C8C93');
        $this->addSql('DROP INDEX IDX_A7CE16A4C54C8C93');
        $this->addSql('ALTER TABLE app_exercises RENAME COLUMN type_id TO type');
    }
}
