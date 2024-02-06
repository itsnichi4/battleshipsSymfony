<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240204154458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE matchmaking (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, status VARCHAR(255) NOT NULL, is_available TINYINT(1) NOT NULL, INDEX IDX_16214298A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE matchmaking ADD CONSTRAINT FK_16214298A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game ADD player1_board_state JSON DEFAULT NULL, ADD player2_board_state JSON DEFAULT NULL, ADD player1_moves JSON DEFAULT NULL, ADD player2_moves JSON DEFAULT NULL, ADD expires_at DATETIME NOT NULL, CHANGE player2_id player2_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_2DA17977F85E0677 ON user');
        $this->addSql('DROP INDEX UNIQ_2DA17977E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, ADD is_available TINYINT(1) NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE matchmaking DROP FOREIGN KEY FK_16214298A76ED395');
        $this->addSql('DROP TABLE matchmaking');
        $this->addSql('ALTER TABLE user DROP roles, DROP is_available, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DA17977F85E0677 ON user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DA17977E7927C74 ON user (email)');
        $this->addSql('ALTER TABLE game DROP player1_board_state, DROP player2_board_state, DROP player1_moves, DROP player2_moves, DROP expires_at, CHANGE player2_id player2_id INT NOT NULL');
    }
}
