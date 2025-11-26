<?php

declare(strict_types=1);

namespace Synolia\SyliusAkeneoPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230425081120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added akeneo taxon attribute tables (PostgreSQL).';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on postgresql.'
        );

        $this->addSql('
            CREATE TABLE akeneo_taxon_attribute_translations (
                id SERIAL NOT NULL,
                translatable_id INT DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                locale VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            );
        ');

        $this->addSql('CREATE INDEX IDX_EDF43DE42C2AC5D3 ON akeneo_taxon_attribute_translations (translatable_id);');
        $this->addSql('CREATE UNIQUE INDEX attribute_translation ON akeneo_taxon_attribute_translations (translatable_id, locale);');

        $this->addSql('
            CREATE TABLE akeneo_taxon_attributes (
                id SERIAL NOT NULL,
                code VARCHAR(255) NOT NULL,
                type VARCHAR(255) NOT NULL,
                configuration TEXT NOT NULL,
                storage_type VARCHAR(255) NOT NULL,
                position INT NOT NULL,
                translatable BOOLEAN NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                PRIMARY KEY(id)
            );
        ');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_C4D4A29777153098 ON akeneo_taxon_attributes (code);');
        $this->addSql('COMMENT ON COLUMN akeneo_taxon_attributes.configuration IS \'(DC2Type:array)\';');

        $this->addSql('
            CREATE TABLE akeneo_taxon_attribute_values (
                id SERIAL NOT NULL,
                attribute_id INT NOT NULL,
                subject_id INT DEFAULT NULL,
                locale_code VARCHAR(255) DEFAULT NULL,
                text_value TEXT DEFAULT NULL,
                boolean_value BOOLEAN DEFAULT NULL,
                integer_value INT DEFAULT NULL,
                float_value DOUBLE PRECISION DEFAULT NULL,
                datetime_value TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                date_value DATE DEFAULT NULL,
                json_value JSON DEFAULT NULL,
                PRIMARY KEY(id)
            );
        ');

        $this->addSql('CREATE INDEX IDX_7AEE551B6E62EFA ON akeneo_taxon_attribute_values (attribute_id);');
        $this->addSql('CREATE INDEX IDX_7AEE55123EDC87 ON akeneo_taxon_attribute_values (subject_id);');
        $this->addSql('CREATE UNIQUE INDEX attribute_value ON akeneo_taxon_attribute_values (subject_id, attribute_id, locale_code);');

        $this->addSql('
            ALTER TABLE akeneo_taxon_attribute_translations
            ADD CONSTRAINT FK_EDF43DE42C2AC5D3
            FOREIGN KEY (translatable_id) REFERENCES akeneo_taxon_attributes (id)
            NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');

        $this->addSql('
            ALTER TABLE akeneo_taxon_attribute_values
            ADD CONSTRAINT FK_7AEE551B6E62EFA
            FOREIGN KEY (attribute_id) REFERENCES akeneo_taxon_attributes (id)
            NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');

        $this->addSql('
            ALTER TABLE akeneo_taxon_attribute_values
            ADD CONSTRAINT FK_7AEE55123EDC87
            FOREIGN KEY (subject_id) REFERENCES sylius_taxon (id)
            NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on postgresql.'
        );

        $this->addSql('
            ALTER TABLE akeneo_taxon_attribute_translations
            DROP CONSTRAINT FK_EDF43DE42C2AC5D3;
        ');

        $this->addSql('
            ALTER TABLE akeneo_taxon_attribute_values
            DROP CONSTRAINT FK_7AEE551B6E62EFA;
        ');

        $this->addSql('DROP TABLE akeneo_taxon_attribute_translations;');
        $this->addSql('DROP TABLE akeneo_taxon_attribute_values;');
        $this->addSql('DROP TABLE akeneo_taxon_attributes;');
    }
}
