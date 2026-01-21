<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251206140208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, movie_id INT NOT NULL, title VARCHAR(255) NOT NULL, comment LONGTEXT NOT NULL, rating INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_794381C6A76ED395 (user_id), INDEX IDX_794381C68F93B6FC (movie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C68F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE director ADD photo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE director ADD CONSTRAINT FK_1E90D3F07E9E4C8C FOREIGN KEY (photo_id) REFERENCES media_object (id)');
        $this->addSql('CREATE INDEX IDX_1E90D3F07E9E4C8C ON director (photo_id)');
        $this->addSql('ALTER TABLE media_object ADD type VARCHAR(50) DEFAULT \'other\' NOT NULL');
        $this->addSql('ALTER TABLE user ADD photo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497E9E4C8C FOREIGN KEY (photo_id) REFERENCES media_object (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6497E9E4C8C ON user (photo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C68F93B6FC');
        $this->addSql('DROP TABLE review');
        $this->addSql('ALTER TABLE media_object DROP type');
        $this->addSql('ALTER TABLE director DROP FOREIGN KEY FK_1E90D3F07E9E4C8C');
        $this->addSql('DROP INDEX IDX_1E90D3F07E9E4C8C ON director');
        $this->addSql('ALTER TABLE director DROP photo_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497E9E4C8C');
        $this->addSql('DROP INDEX IDX_8D93D6497E9E4C8C ON user');
        $this->addSql('ALTER TABLE user DROP photo_id');
    }
}
