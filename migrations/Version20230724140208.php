<?php

declare(strict_types=1);

namespace Synolia\SyliusAkeneoPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230724140208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Changed akeneo product group foreign keys (PostgreSQL).';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on postgresql.'
        );

        $this->addSql('TRUNCATE TABLE akeneo_productgroup_product;');

        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            DROP CONSTRAINT FK_15F96A1C4584665A;
        ');
        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            DROP CONSTRAINT FK_15F96A1C5BC5238A;
        ');

        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            DROP CONSTRAINT akeneo_productgroup_product_pkey;
        ');

        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            ADD CONSTRAINT FK_15F96A1C4584665A
            FOREIGN KEY ("product_id") REFERENCES akeneo_product_group (id)
            NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');

        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            ADD CONSTRAINT FK_15F96A1C5BC5238A
            FOREIGN KEY ("productgroup_id") REFERENCES sylius_product (id)
            NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');

        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            ADD PRIMARY KEY ("product_id", "productgroup_id");
        ');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on postgresql.'
        );

        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            DROP CONSTRAINT FK_15F96A1C4584665A;
        ');
        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            DROP CONSTRAINT FK_15F96A1C5BC5238A;
        ');

        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            DROP CONSTRAINT akeneo_productgroup_product_pkey;
        ');

        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            ADD CONSTRAINT FK_15F96A1C4584665A
            FOREIGN KEY ("product_id") REFERENCES sylius_product (id)
            ON DELETE CASCADE
            NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');

        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            ADD CONSTRAINT FK_15F96A1C5BC5238A
            FOREIGN KEY ("productgroup_id") REFERENCES akeneo_product_group (id)
            ON DELETE CASCADE
            NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');

        $this->addSql('
            ALTER TABLE akeneo_productgroup_product
            ADD PRIMARY KEY ("productgroup_id", "product_id");
        ');
    }
}
