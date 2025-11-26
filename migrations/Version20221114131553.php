<?php

declare(strict_types=1);

namespace Synolia\SyliusAkeneoPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221114131553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added associations column (PostgreSQL).';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on postgresql.'
        );

        // Dodajemy kolumnę TEXT (zamiast LONGTEXT)
        $this->addSql('ALTER TABLE akeneo_product_group ADD COLUMN associations TEXT NOT NULL;');
        $this->addSql('COMMENT ON COLUMN akeneo_product_group.associations IS \'(DC2Type:array)\';');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on postgresql.'
        );

        $this->addSql('ALTER TABLE akeneo_product_group DROP COLUMN associations;');
    }
}
