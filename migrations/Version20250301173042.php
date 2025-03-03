<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250301173042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE recipe (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, preparation_time INT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, serving INT NOT NULL, INDEX IDX_DA88B137F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_recipe_category (recipe_id INT NOT NULL, recipe_category_id INT NOT NULL, INDEX IDX_19A8E93359D8A214 (recipe_id), INDEX IDX_19A8E933C6B4D386 (recipe_category_id), PRIMARY KEY(recipe_id, recipe_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_ingredient (id INT AUTO_INCREMENT NOT NULL, recipe_id_id INT NOT NULL, ingredient_id_id INT NOT NULL, quantity VARCHAR(255) DEFAULT NULL, INDEX IDX_22D1FE1369574A48 (recipe_id_id), INDEX IDX_22D1FE136676F996 (ingredient_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, birth_date DATETIME NOT NULL, password VARCHAR(255) NOT NULL, newsletter TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B137F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recipe_recipe_category ADD CONSTRAINT FK_19A8E93359D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_recipe_category ADD CONSTRAINT FK_19A8E933C6B4D386 FOREIGN KEY (recipe_category_id) REFERENCES recipe_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_ingredient ADD CONSTRAINT FK_22D1FE1369574A48 FOREIGN KEY (recipe_id_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_ingredient ADD CONSTRAINT FK_22D1FE136676F996 FOREIGN KEY (ingredient_id_id) REFERENCES ingredient (id)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B137F675F31B');
        $this->addSql('ALTER TABLE recipe_recipe_category DROP FOREIGN KEY FK_19A8E93359D8A214');
        $this->addSql('ALTER TABLE recipe_recipe_category DROP FOREIGN KEY FK_19A8E933C6B4D386');
        $this->addSql('ALTER TABLE recipe_ingredient DROP FOREIGN KEY FK_22D1FE1369574A48');
        $this->addSql('ALTER TABLE recipe_ingredient DROP FOREIGN KEY FK_22D1FE136676F996');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('DROP TABLE recipe_recipe_category');
        $this->addSql('DROP TABLE recipe_category');
        $this->addSql('DROP TABLE recipe_ingredient');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
