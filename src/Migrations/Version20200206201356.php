<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200206201356 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, montant INT NOT NULL, frais INT NOT NULL, nom_complet_e VARCHAR(255) NOT NULL, type_piece_e VARCHAR(255) NOT NULL, numero_piece_e INT NOT NULL, date_envoi DATETIME NOT NULL, telephone_e INT NOT NULL, commission_e DOUBLE PRECISION NOT NULL, nom_complet_r VARCHAR(255) NOT NULL, type_piece_r VARCHAR(255) DEFAULT NULL, numero_piece_r INT DEFAULT NULL, telephone_r INT NOT NULL, date_retrait DATETIME DEFAULT NULL, commision_r DOUBLE PRECISION DEFAULT NULL, commission_systeme DOUBLE PRECISION DEFAULT NULL, taxe_etat DOUBLE PRECISION DEFAULT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE transaction');
    }
}
