<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611095321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorito (id INT AUTO_INCREMENT NOT NULL, id_usuario_id INT NOT NULL, id_producto_id INT NOT NULL, INDEX IDX_881067C77EB2C349 (id_usuario_id), INDEX IDX_881067C76E57A479 (id_producto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favorito ADD CONSTRAINT FK_881067C77EB2C349 FOREIGN KEY (id_usuario_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE favorito ADD CONSTRAINT FK_881067C76E57A479 FOREIGN KEY (id_producto_id) REFERENCES producto (id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorito DROP FOREIGN KEY FK_881067C77EB2C349');
        $this->addSql('ALTER TABLE favorito DROP FOREIGN KEY FK_881067C76E57A479');
        $this->addSql('DROP TABLE favorito');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
