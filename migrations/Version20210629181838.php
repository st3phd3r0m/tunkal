<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210629181838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, pseudo VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, is_moderated TINYINT(1) NOT NULL, sent_at DATETIME NOT NULL, INDEX IDX_5F9E962A4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, posts_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E01FBE6AD5E258C5 (posts_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE links (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, hyperlink VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, position VARCHAR(255) DEFAULT NULL, position_order INT DEFAULT NULL, alt VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL, is_active TINYINT(1) DEFAULT NULL, is_video TINYINT(1) DEFAULT NULL, uploaded_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE posts (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, meta_title VARCHAR(255) NOT NULL, meta_description LONGTEXT NOT NULL, content LONGTEXT NOT NULL, keywords JSON DEFAULT NULL, meta_keywords JSON DEFAULT NULL, created_at DATETIME NOT NULL, is_past_concert TINYINT(1) NOT NULL, slug VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_885DBAFA989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shows (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, meta_description LONGTEXT NOT NULL, meta_title VARCHAR(255) NOT NULL, expected_at DATETIME NOT NULL, image VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, description LONGTEXT NOT NULL, meta_keywords JSON DEFAULT NULL, keywords JSON DEFAULT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6C3BF144989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A4B89032C FOREIGN KEY (post_id) REFERENCES posts (id)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AD5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A4B89032C');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AD5E258C5');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE links');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE shows');
        $this->addSql('DROP TABLE users');
    }
}
