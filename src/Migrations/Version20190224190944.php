<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190224190944 extends AbstractMigration
{
    private const PRODUCT_TYPES = [
        'defined' => 'This product can only be selected by an administrator.',
        'selected' => 'This product can be selected by an end-user.'
    ];

    public function getDescription() : string
    {
        return 'Product, Product type, Tax rate';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product (id VARCHAR(255) NOT NULL, tax_rate_id VARCHAR(255) NOT NULL, type_id VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_D34A04ADFDD13F95 (tax_rate_id), INDEX IDX_D34A04ADC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_type (name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tax_rate (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, percentage DOUBLE PRECISION NOT NULL, type VARCHAR(255) NOT NULL, show_tax TINYINT(1) NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADFDD13F95 FOREIGN KEY (tax_rate_id) REFERENCES tax_rate (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADC54C8C93 FOREIGN KEY (type_id) REFERENCES product_type (name)');

        foreach (static::PRODUCT_TYPES as $name => $description) {
            $this->addSql(
                'INSERT INTO product_type (name, description) VALUES (:name, :description)',
                [
                    ':name' => $name,
                    ':description' => $description
                ]
            );
        }
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADC54C8C93');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADFDD13F95');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_type');
        $this->addSql('DROP TABLE tax_rate');
    }
}
