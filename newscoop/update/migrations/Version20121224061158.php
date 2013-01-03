<?php

namespace NewscoopMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121224061158 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE addresses33 (id INT NOT NULL, street VARCHAR(255) NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE addresses33');
    }

    public function getName()
    {
      return '4.1_test_name';
    }
}
