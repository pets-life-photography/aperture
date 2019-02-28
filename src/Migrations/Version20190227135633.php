<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190227135633 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Photo, Photo type, Photo version, Watermark, Watermark configuration';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE photo_type (name VARCHAR(255) NOT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, watermark TINYINT(1) NOT NULL, crop TINYINT(1) NOT NULL, available TINYINT(1) NOT NULL, PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE watermark_configuration (id INT AUTO_INCREMENT NOT NULL, watermark_id INT NOT NULL, opacity INT NOT NULL, position VARCHAR(255) NOT NULL, crop TINYINT(1) NOT NULL, padding_x DOUBLE PRECISION NOT NULL, padding_y DOUBLE PRECISION NOT NULL, padding_unit VARCHAR(2) NOT NULL, INDEX IDX_9A5B71DE6282BE5D (watermark_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE watermark (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, width INT NOT NULL, height INT NOT NULL, content_type VARCHAR(255) NOT NULL, content LONGBLOB NOT NULL, UNIQUE INDEX name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo_version (id INT AUTO_INCREMENT NOT NULL, photo_id INT NOT NULL, type_id VARCHAR(255) NOT NULL, content_type VARCHAR(255) NOT NULL, content LONGBLOB NOT NULL, hash VARCHAR(255) NOT NULL, width INT NOT NULL, height INT NOT NULL, available TINYINT(1) NOT NULL, INDEX IDX_8AFCC44E7E9E4C8C (photo_id), INDEX IDX_8AFCC44EC54C8C93 (type_id), UNIQUE INDEX photo_type (photo_id, type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, watermark_configuration_id INT NOT NULL, content LONGBLOB NOT NULL, content_type VARCHAR(255) NOT NULL, meta_data JSON NOT NULL COMMENT \'(DC2Type:json_array)\', width INT NOT NULL, height INT NOT NULL, INDEX IDX_14B784182BCB1D5B (watermark_configuration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE watermark_configuration ADD CONSTRAINT FK_9A5B71DE6282BE5D FOREIGN KEY (watermark_id) REFERENCES watermark (id)');
        $this->addSql('ALTER TABLE photo_version ADD CONSTRAINT FK_8AFCC44E7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id)');
        $this->addSql('ALTER TABLE photo_version ADD CONSTRAINT FK_8AFCC44EC54C8C93 FOREIGN KEY (type_id) REFERENCES photo_type (name)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784182BCB1D5B FOREIGN KEY (watermark_configuration_id) REFERENCES watermark_configuration (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE photo_version DROP FOREIGN KEY FK_8AFCC44EC54C8C93');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B784182BCB1D5B');
        $this->addSql('ALTER TABLE watermark_configuration DROP FOREIGN KEY FK_9A5B71DE6282BE5D');
        $this->addSql('ALTER TABLE photo_version DROP FOREIGN KEY FK_8AFCC44E7E9E4C8C');
        $this->addSql('DROP TABLE photo_type');
        $this->addSql('DROP TABLE watermark_configuration');
        $this->addSql('DROP TABLE watermark');
        $this->addSql('DROP TABLE photo_version');
        $this->addSql('DROP TABLE photo');
    }
}
