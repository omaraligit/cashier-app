<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210325204648 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE vat_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE vat_type (id INT NOT NULL, value INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE receipt_product DROP quantity');
        $this->addSql('ALTER TABLE "user" ADD api_token VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE vat_type_id_seq CASCADE');
        $this->addSql('DROP TABLE vat_type');
        $this->addSql('ALTER TABLE "user" DROP api_token');
        $this->addSql('ALTER TABLE receipt_product ADD quantity INT DEFAULT 1 NOT NULL');
    }
}
