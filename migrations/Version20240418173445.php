<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240418173445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, keycloak_id VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categorie CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE name name VARCHAR(50) DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('DROP INDEX `primary` ON formation');
        $this->addSql('ALTER TABLE formation CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE title title VARCHAR(100) DEFAULT NULL, CHANGE playlist_id playlist_id INT DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE video_id video_id VARCHAR(20) DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_404021BF6BBD148 ON formation (playlist_id)');
        $this->addSql('ALTER TABLE formation ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE formation_categorie ADD PRIMARY KEY (formation_id, categorie_id)');
        $this->addSql('CREATE INDEX IDX_830086E95200282E ON formation_categorie (formation_id)');
        $this->addSql('CREATE INDEX IDX_830086E9BCF5E72D ON formation_categorie (categorie_id)');
        $this->addSql('ALTER TABLE playlist CHANGE id id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE categorie MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON categorie');
        $this->addSql('ALTER TABLE categorie CHANGE id id INT NOT NULL, CHANGE name name VARCHAR(50) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE formation MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BF6BBD148');
        $this->addSql('DROP INDEX IDX_404021BF6BBD148 ON formation');
        $this->addSql('DROP INDEX `PRIMARY` ON formation');
        $this->addSql('ALTER TABLE formation CHANGE id id INT NOT NULL, CHANGE playlist_id playlist_id INT NOT NULL, CHANGE title title INT NOT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE video_id video_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE formation ADD PRIMARY KEY (id, title, playlist_id)');
        $this->addSql('ALTER TABLE formation_categorie DROP FOREIGN KEY FK_830086E95200282E');
        $this->addSql('ALTER TABLE formation_categorie DROP FOREIGN KEY FK_830086E9BCF5E72D');
        $this->addSql('DROP INDEX IDX_830086E95200282E ON formation_categorie');
        $this->addSql('DROP INDEX IDX_830086E9BCF5E72D ON formation_categorie');
        $this->addSql('DROP INDEX `primary` ON formation_categorie');
        $this->addSql('ALTER TABLE messenger_messages MODIFY id BIGINT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages');
        $this->addSql('ALTER TABLE messenger_messages CHANGE id id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE playlist MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON playlist');
        $this->addSql('ALTER TABLE playlist CHANGE id id INT NOT NULL');
    }
}
