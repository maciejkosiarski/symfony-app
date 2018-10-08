<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181008203454 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE app_company_probes_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE app_company_watchers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_company_watchers (id INT NOT NULL, user_id INT NOT NULL, company_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_48755BE2A76ED395 ON app_company_watchers (user_id)');
        $this->addSql('CREATE INDEX IDX_48755BE2979B1AD6 ON app_company_watchers (company_id)');
        $this->addSql('ALTER TABLE app_company_watchers ADD CONSTRAINT FK_48755BE2A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_company_watchers ADD CONSTRAINT FK_48755BE2979B1AD6 FOREIGN KEY (company_id) REFERENCES app_companies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE app_company_probes');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A2BF50F5E237E06 ON app_companies (name)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE app_company_watchers_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE app_company_probes_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_company_probes (id INT NOT NULL, user_id INT NOT NULL, company_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_2207ae39a76ed395 ON app_company_probes (user_id)');
        $this->addSql('CREATE INDEX idx_2207ae39979b1ad6 ON app_company_probes (company_id)');
        $this->addSql('ALTER TABLE app_company_probes ADD CONSTRAINT fk_2207ae39a76ed395 FOREIGN KEY (user_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_company_probes ADD CONSTRAINT fk_2207ae39979b1ad6 FOREIGN KEY (company_id) REFERENCES app_companies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE app_company_watchers');
        $this->addSql('DROP INDEX UNIQ_2A2BF50F5E237E06');
    }
}
