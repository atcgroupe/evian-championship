<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220509144222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, delivery_id INT DEFAULT NULL, product_id INT NOT NULL, customer_reference VARCHAR(100) NOT NULL, location VARCHAR(100) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, width INT NOT NULL, height INT NOT NULL, left_bleed INT NOT NULL, right_bleed INT NOT NULL, top_bleed INT NOT NULL, bottom_bleed INT NOT NULL, finish VARCHAR(255) DEFAULT NULL, image_count INT NOT NULL, image_quantity INT NOT NULL, customer_comment LONGTEXT DEFAULT NULL, validation_comment LONGTEXT DEFAULT NULL, reject_comment LONGTEXT DEFAULT NULL, status SMALLINT NOT NULL, INDEX IDX_FBD8E0F812136921 (delivery_id), INDEX IDX_FBD8E0F84584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_file (id INT AUTO_INCREMENT NOT NULL, job_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_B534051BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE validation_file (id INT AUTO_INCREMENT NOT NULL, job_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_274E1657BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F812136921 FOREIGN KEY (delivery_id) REFERENCES delivery (id)');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F84584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE job_file ADD CONSTRAINT FK_B534051BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE validation_file ADD CONSTRAINT FK_274E1657BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_file DROP FOREIGN KEY FK_B534051BE04EA9');
        $this->addSql('ALTER TABLE validation_file DROP FOREIGN KEY FK_274E1657BE04EA9');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE job_file');
        $this->addSql('DROP TABLE validation_file');
    }
}
