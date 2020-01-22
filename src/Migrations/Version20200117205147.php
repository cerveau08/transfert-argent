<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200117205147 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE depot ADD caissier_add_id INT NOT NULL');
        $this->addSql('ALTER TABLE depot ADD CONSTRAINT FK_47948BBCAA806084 FOREIGN KEY (caissier_add_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_47948BBCAA806084 ON depot (caissier_add_id)');
        $this->addSql('ALTER TABLE compte ADD admin_createur_id INT NOT NULL');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF65260EEB848EC FOREIGN KEY (admin_createur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CFF65260EEB848EC ON compte (admin_createur_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF65260EEB848EC');
        $this->addSql('DROP INDEX IDX_CFF65260EEB848EC ON compte');
        $this->addSql('ALTER TABLE compte DROP admin_createur_id');
        $this->addSql('ALTER TABLE depot DROP FOREIGN KEY FK_47948BBCAA806084');
        $this->addSql('DROP INDEX IDX_47948BBCAA806084 ON depot');
        $this->addSql('ALTER TABLE depot DROP caissier_add_id');
    }
}
