<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Set existing user rate_limit values to NULL to use system default
 */
final class Version20251210132310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Set existing user rate_limit values to NULL to use system default';
    }

    public function up(Schema $schema): void
    {
        // Update all existing rate_limit values to NULL
        $this->addSql('UPDATE user SET rate_limit = NULL WHERE rate_limit = 100');
    }

    public function down(Schema $schema): void
    {
        // Restore rate_limit to 100 for users who had NULL
        $this->addSql('UPDATE user SET rate_limit = 100 WHERE rate_limit IS NULL');
    }
}