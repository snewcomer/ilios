<?php
declare(strict_types=1);

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds position column to objective table.
 */
class Version20170314175718 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE objective ADD position INT NOT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE objective DROP position');
    }
}
