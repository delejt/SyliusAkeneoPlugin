<?php

declare(strict_types=1);

namespace Synolia\SyliusAkeneoPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231019090303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Allow to delete cascade product group (PostgreSQL).';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on postgresql.'
        );

        $this->addSql('
            ALTER TABLE akeneo_product_group
            DROP CONSTRAINT FK_52E48776727ACA70;
        ');

        $this->addSql('
            ALTER TABLE akeneo_product_group
            ADD CONSTRAINT FK_52E48776727ACA70
            FOREIGN KEY (parent_id) REFERENCES akeneo_product_group (id)
            ON DELETE CASCADE
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
            ALTER TABLE akeneo_product_group
            DROP CONSTRAINT FK_52E48776727ACA70;
        ');

        $this->addSql('
            ALTER TABLE akeneo_product_group
            ADD CONSTRAINT FK_52E48776727ACA70
            FOREIGN KEY (parent_id) REFERENCES akeneo_product_group (id)
            NOT DEFERRABLE INITIALLY IMMEDIATE;
        ');
    }
}
