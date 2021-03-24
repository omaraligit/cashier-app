<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210324183553 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE receipt_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE receipt (id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, closed_at TIMESTAMP(0) WITHOUT TIME ZONE, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE receipt_product (receipt_id INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(receipt_id, product_id))');
        $this->addSql('CREATE INDEX IDX_C000A1532B5CA896 ON receipt_product (receipt_id)');
        $this->addSql('CREATE INDEX IDX_C000A1534584665A ON receipt_product (product_id)');
        $this->addSql('ALTER TABLE receipt_product ADD CONSTRAINT FK_C000A1532B5CA896 FOREIGN KEY (receipt_id) REFERENCES receipt (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE receipt_product ADD CONSTRAINT FK_C000A1534584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE receipt_product DROP CONSTRAINT FK_C000A1532B5CA896');
        $this->addSql('DROP SEQUENCE receipt_id_seq CASCADE');
        $this->addSql('DROP TABLE receipt');
        $this->addSql('DROP TABLE receipt_product');
    }
}
