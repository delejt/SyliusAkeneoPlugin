<?php

declare(strict_types=1);

namespace Synolia\SyliusAkeneoPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231106080715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added useAkeneoPositions column (PostgreSQL).';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on postgresql.'
        );

        $this->addSql('ALTER TABLE akeneo_api_configuration_categories ADD COLUMN "useakeneopositions" BOOLEAN NOT NULL;');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on postgresql.'
        );

        $this->addSql('ALTER TABLE akeneo_api_configuration_categories DROP COLUMN "useakeneopositions";');
    }
}
