<?php

declare(strict_types=1);

namespace Synolia\SyliusAkeneoPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221107103437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added asset table (PostgreSQL).';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on postgresql.'
        );

        // akeneo_assets
        $this->addSql('
            CREATE TABLE akeneo_assets (
                id SERIAL NOT NULL,
                family_code VARCHAR(255) NOT NULL,
                asset_code VARCHAR(255) NOT NULL,
                attribute_code VARCHAR(255) NOT NULL,
                type VARCHAR(255) NOT NULL,
                locale VARCHAR(255) NOT NULL,
                scope VARCHAR(255) NOT NULL,
                content JSON NOT NULL,
                PRIMARY KEY(id)
            );
        ');

        // akeneo_assets_products (pivot productów)
        $this->addSql('
            CREATE TABLE akeneo_assets_products (
                asset_id INT NOT NULL,
                owner_id INT NOT NULL,
                PRIMARY KEY(asset_id, owner_id)
            );
        ');

        $this->addSql('CREATE INDEX IDX_397D5EBB5DA1941 ON akeneo_assets_products (asset_id);');
        $this->addSql('CREATE INDEX IDX_397D5EBB7E3C61F9 ON akeneo_assets_products (owner_id);');

        // akeneo_assets_product_variants (pivot wariantów)
        $this->addSql('
            CREATE TABLE akeneo_assets_product_variants (
                asset_id INT NOT NULL,
                variant_id INT NOT NULL,
                PRIMARY KEY(asset_id, variant_id)
            );
        ');

        $this->addSql('CREATE INDEX IDX_34A6BEA55DA1941 ON akeneo_assets_product_variants (asset_id);');
        $this->addSql('CREATE INDEX IDX_34A6BEA53B69A9AF ON akeneo_assets_product_variants (variant_id);');

        // Klucze obce
        $this->addSql('
            ALTER TABLE akeneo_assets_products
            ADD CONSTRAINT FK_397D5EBB5DA1941
            FOREIGN KEY (asset_id) REFERENCES akeneo_assets (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');

        $this->addSql('
            ALTER TABLE akeneo_assets_products
            ADD CONSTRAINT FK_397D5EBB7E3C61F9
            FOREIGN KEY (owner_id) REFERENCES sylius_product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');

        $this->addSql('
            ALTER TABLE akeneo_assets_product_variants
            ADD CONSTRAINT FK_34A6BEA55DA1941
            FOREIGN KEY (asset_id) REFERENCES akeneo_assets (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');

        $this->addSql('
            ALTER TABLE akeneo_assets_product_variants
            ADD CONSTRAINT FK_34A6BEA53B69A9AF
            FOREIGN KEY (variant_id) REFERENCES sylius_product_variant (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on postgresql.'
        );

        // Najpierw zdejmujemy FK, potem tabele

        $this->addSql('
            ALTER TABLE akeneo_assets_products
            DROP CONSTRAINT FK_397D5EBB5DA1941;
        ');

        $this->addSql('
            ALTER TABLE akeneo_assets_products
            DROP CONSTRAINT FK_397D5EBB7E3C61F9;
        ');

        $this->addSql('
            ALTER TABLE akeneo_assets_product_variants
            DROP CONSTRAINT FK_34A6BEA55DA1941;
        ');

        $this->addSql('
            ALTER TABLE akeneo_assets_product_variants
            DROP CONSTRAINT FK_34A6BEA53B69A9AF;
        ');

        $this->addSql('DROP TABLE akeneo_assets_product_variants;');
        $this->addSql('DROP TABLE akeneo_assets_products;');
        $this->addSql('DROP TABLE akeneo_assets;');
    }
}
