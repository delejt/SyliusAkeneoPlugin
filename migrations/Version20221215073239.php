<?php

declare(strict_types=1);

namespace Synolia\SyliusAkeneoPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221215073239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Refactored Akeneo ProductGroup table (PostgreSQL).';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on postgresql.'
        );

        // W pierwszej migracji mieliśmy:
        // CREATE UNIQUE INDEX UNIQ_52E487761146F2B9 ON akeneo_product_group ("productParent");
        // W Postgresie DROP INDEX nie podaje tabeli, tylko nazwę indeksu.
        $this->addSql('DROP INDEX UNIQ_52E487761146F2B9;');

        // Dodajemy nowe kolumny
        $this->addSql('ALTER TABLE akeneo_product_group ADD COLUMN parent_id INT DEFAULT NULL;');
        $this->addSql('ALTER TABLE akeneo_product_group ADD COLUMN "familyvariant" VARCHAR(255) NOT NULL;');

        // W MySQL było: CHANGE productparent model VARCHAR(255) NOT NULL
        // U nas kolumna nazywa się "productParent" → robimy rename na "model"
        $this->addSql('ALTER TABLE akeneo_product_group RENAME COLUMN "productparent" TO model;');
        // Na wszelki wypadek doprecyzowujemy typ + NOT NULL (choć rename zwykle to zachowuje)
        $this->addSql('ALTER TABLE akeneo_product_group ALTER COLUMN model TYPE VARCHAR(255);');
        $this->addSql('ALTER TABLE akeneo_product_group ALTER COLUMN model SET NOT NULL;');

        // Klucz obcy parent_id → akeneo_product_group(id)
        $this->addSql('
            ALTER TABLE akeneo_product_group
            ADD CONSTRAINT FK_52E48776727ACA70
            FOREIGN KEY (parent_id) REFERENCES akeneo_product_group (id)
            NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');

        // Nowy unikalny index na model
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52E48776D79572D9 ON akeneo_product_group (model);');

        // Index po parent_id
        $this->addSql('CREATE INDEX IDX_52E48776727ACA70 ON akeneo_product_group (parent_id);');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on postgresql.'
        );

        // Zdejmujemy FK i indeksy
        $this->addSql('
            ALTER TABLE akeneo_product_group
            DROP CONSTRAINT FK_52E48776727ACA70;
        ');

        $this->addSql('DROP INDEX UNIQ_52E48776D79572D9;');
        $this->addSql('DROP INDEX IDX_52E48776727ACA70;');

        // Przywracamy starą nazwę kolumny model → "productParent"
        $this->addSql('ALTER TABLE akeneo_product_group RENAME COLUMN model TO "productparent";');

        // Usuwamy nowe kolumny
        $this->addSql('ALTER TABLE akeneo_product_group DROP COLUMN parent_id;');
        $this->addSql('ALTER TABLE akeneo_product_group DROP COLUMN "familyvariant";');

        // Przywracamy stary unikalny indeks na "productParent"
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52E487761146F2B9 ON akeneo_product_group ("productparent");');
    }
}
