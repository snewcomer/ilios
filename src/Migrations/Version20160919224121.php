<?php
declare(strict_types=1);

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Cascade learner group deletes to subgroups
 */
class Version20160919224121 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C561997596');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C561997596 FOREIGN KEY (parent_group_id) REFERENCES `group` (group_id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C561997596');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C561997596 FOREIGN KEY (parent_group_id) REFERENCES `group` (group_id)');
    }
}
