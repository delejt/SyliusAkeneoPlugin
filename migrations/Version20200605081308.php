<?php

declare(strict_types=1);

namespace Synolia\SyliusAkeneoPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200605081308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Akeneo plugin tables';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on postgresql.'
        );

        // akeneo_api_configuration_product
        $this->addSql('
            CREATE TABLE akeneo_api_configuration_product (
                id SERIAL NOT NULL,
                "akeneopriceattribute" VARCHAR(255) DEFAULT NULL,
                "akeneoenabledchannelsattribute" VARCHAR(255) DEFAULT NULL,
                "attributemapping" TEXT DEFAULT NULL,
                "importmediafiles" BOOLEAN DEFAULT NULL,
                "regenerateurlrewrites" BOOLEAN DEFAULT NULL,
                PRIMARY KEY(id)
            );
        ');

        $this->addSql('COMMENT ON COLUMN akeneo_api_configuration_product."attributemapping" IS \'(DC2Type:array)\';');

        // akeneo_attribute_akeneo_sylius_mapping
        $this->addSql('
            CREATE TABLE akeneo_attribute_akeneo_sylius_mapping (
                id SERIAL NOT NULL,
                "akeneoattribute" VARCHAR(255) NOT NULL,
                "syliusattribute" VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            );
        ');

        // akeneo_settings
        $this->addSql('
            CREATE TABLE akeneo_settings (
                id SERIAL NOT NULL,
                name VARCHAR(255) NOT NULL,
                value VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY(id)
            );
        ');

        // akeneo_api_configuration_categories
        $this->addSql('
            CREATE TABLE akeneo_api_configuration_categories (
                id SERIAL NOT NULL,
                "notimportcategories" TEXT NOT NULL,
                "rootcategories" TEXT NOT NULL,
                PRIMARY KEY(id)
            );
        ');

        $this->addSql('COMMENT ON COLUMN akeneo_api_configuration_categories."notimportcategories" IS \'(DC2Type:array)\';');
        $this->addSql('COMMENT ON COLUMN akeneo_api_configuration_categories."rootcategories" IS \'(DC2Type:array)\';');

        // akeneo_api_configuration_product_images_mapping
        $this->addSql('
            CREATE TABLE akeneo_api_configuration_product_images_mapping (
                id SERIAL NOT NULL,
                "syliusattribute" VARCHAR(255) NOT NULL,
                "akeneoattribute" VARCHAR(255) NOT NULL,
                "productconfiguration_id" INT NOT NULL,
                PRIMARY KEY(id)
            );
        ');

        $this->addSql('CREATE INDEX IDX_A39A907D2B9CB4D4 ON akeneo_api_configuration_product_images_mapping ("productconfiguration_id");');

        // akeneo_attribute_type_mapping
        $this->addSql('
            CREATE TABLE akeneo_attribute_type_mapping (
                id SERIAL NOT NULL,
                "akeneoattributetype" VARCHAR(255) NOT NULL,
                "attributetype" VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            );
        ');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_FF5E270FA2851109 ON akeneo_attribute_type_mapping ("akeneoattributetype");');

        // akeneo_api_product_filters_rules
        $this->addSql('
            CREATE TABLE akeneo_api_product_filters_rules (
                id SERIAL NOT NULL,
                mode VARCHAR(255) NOT NULL,
                "advancedfilter" VARCHAR(255) DEFAULT NULL,
                "completenesstype" VARCHAR(255) DEFAULT NULL,
                locales TEXT NOT NULL,
                "completenessvalue" INT NOT NULL,
                status VARCHAR(255) DEFAULT NULL,
                "updatedmode" VARCHAR(255) DEFAULT NULL,
                "updatedbefore" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                "updatedafter" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                updated INT DEFAULT NULL,
                "excludefamilies" TEXT NOT NULL,
                channel VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            );
        ');

        $this->addSql('COMMENT ON COLUMN akeneo_api_product_filters_rules.locales IS \'(DC2Type:array)\';');
        $this->addSql('COMMENT ON COLUMN akeneo_api_product_filters_rules."excludefamilies" IS \'(DC2Type:array)\';');

        // akeneo_product_group
        $this->addSql('
            CREATE TABLE akeneo_product_group (
                id SERIAL NOT NULL,
                "productparent" VARCHAR(255) NOT NULL,
                "variationaxes" TEXT NOT NULL,
                PRIMARY KEY(id)
            );
        ');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_52E487761146F2B9 ON akeneo_product_group ("productparent");');
        $this->addSql('COMMENT ON COLUMN akeneo_product_group."variationaxes" IS \'(DC2Type:array)\';');

        // akeneo_productgroup_product (tabela pivot)
        $this->addSql('
            CREATE TABLE akeneo_productgroup_product (
                "productgroup_id" INT NOT NULL,
                "product_id" INT NOT NULL,
                PRIMARY KEY("productgroup_id", "product_id")
            );
        ');

        $this->addSql('CREATE INDEX IDX_15F96A1C5BC5238A ON akeneo_productgroup_product ("productgroup_id");');
        $this->addSql('CREATE INDEX IDX_15F96A1C4584665A ON akeneo_productgroup_product ("product_id");');

        // akeneo_api_configuration_product_akeneo_image_attribute
        $this->addSql('
            CREATE TABLE akeneo_api_configuration_product_akeneo_image_attribute (
                id SERIAL NOT NULL,
                "akeneoattributes" VARCHAR(255) NOT NULL,
                "productconfiguration_id" INT NOT NULL,
                PRIMARY KEY(id)
            );
        ');

        $this->addSql('CREATE INDEX IDX_739EBA822B9CB4D4 ON akeneo_api_configuration_product_akeneo_image_attribute ("productconfiguration_id");');

        // Klucze obce
        $this->addSql('
            ALTER TABLE akeneo_api_configuration_product_images_mapping
            ADD CONSTRAINT FK_A39A907D2B9CB4D4
            FOREIGN KEY ("productconfiguration_id") REFERENCES akeneo_api_configuration_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');

        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            ADD CONSTRAINT FK_15F96A1C5BC5238A
            FOREIGN KEY ("productgroup_id") REFERENCES akeneo_product_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');

        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            ADD CONSTRAINT FK_15F96A1C4584665A
            FOREIGN KEY ("product_id") REFERENCES sylius_product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');

        $this->addSql('
            ALTER TABLE akeneo_api_configuration_product_akeneo_image_attribute
            ADD CONSTRAINT FK_739EBA822B9CB4D4
            FOREIGN KEY ("productconfiguration_id") REFERENCES akeneo_api_configuration_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on postgresql.'
        );

        // Najpierw zdejmujemy FK
        $this->addSql('
            ALTER TABLE akeneo_api_configuration_product_images_mapping
            DROP CONSTRAINT FK_A39A907D2B9CB4D4;
        ');

        $this->addSql('
            ALTER TABLE akeneo_api_configuration_product_akeneo_image_attribute
            DROP CONSTRAINT FK_739EBA822B9CB4D4;
        ');

        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            DROP CONSTRAINT FK_15F96A1C5BC5238A;
        ');

        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            DROP CONSTRAINT FK_15F96A1C4584665A;
        ');

        $this->addSql('DROP TABLE akeneo_api_configuration_product_akeneo_image_attribute;');
        $this->addSql('DROP TABLE akeneo_productgroup_product;');
        $this->addSql('DROP TABLE akeneo_product_group;');
        $this->addSql('DROP TABLE akeneo_api_product_filters_rules;');
        $this->addSql('DROP TABLE akeneo_attribute_type_mapping;');
        $this->addSql('DROP TABLE akeneo_api_configuration_product_images_mapping;');
        $this->addSql('DROP TABLE akeneo_api_configuration_categories;');
        $this->addSql('DROP TABLE akeneo_settings;');
        $this->addSql('DROP TABLE akeneo_attribute_akeneo_sylius_mapping;');
        $this->addSql('DROP TABLE akeneo_api_configuration_product;');
    }
}
