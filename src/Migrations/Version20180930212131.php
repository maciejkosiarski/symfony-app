<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180930212131 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE app_shares_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE app_company_shares_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_companies_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_company_sources_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_company_shares (id INT NOT NULL, company_id INT NOT NULL, price DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_723A87F7979B1AD6 ON app_company_shares (company_id)');
        $this->addSql('CREATE TABLE app_companies (id INT NOT NULL, name VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE app_company_sources (id INT NOT NULL, company_id INT NOT NULL, path VARCHAR(255) NOT NULL, price_selector VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A8D55AAC979B1AD6 ON app_company_sources (company_id)');
        $this->addSql('ALTER TABLE app_company_shares ADD CONSTRAINT FK_723A87F7979B1AD6 FOREIGN KEY (company_id) REFERENCES app_companies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_company_sources ADD CONSTRAINT FK_A8D55AAC979B1AD6 FOREIGN KEY (company_id) REFERENCES app_companies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE app_shares');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE app_company_shares DROP CONSTRAINT FK_723A87F7979B1AD6');
        $this->addSql('ALTER TABLE app_company_sources DROP CONSTRAINT FK_A8D55AAC979B1AD6');
        $this->addSql('DROP SEQUENCE app_company_shares_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_companies_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_company_sources_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE app_shares_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_shares (id INT NOT NULL, company VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE app_company_shares');
        $this->addSql('DROP TABLE app_companies');
        $this->addSql('DROP TABLE app_company_sources');
    }
}
