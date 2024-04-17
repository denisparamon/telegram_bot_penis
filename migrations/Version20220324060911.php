<?php

declare(strict_types=1);

namespace PenisBot\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220324060911 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание таблицы gigachads';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
CREATE TABLE IF NOT EXISTS members (
    id INTEGER PRIMARY KEY,
    user_telegram_id INTEGER NOT NULL,
    chat_telegram_id INTEGER NOT NULL,
    username TEXT NOT NULL
);
SQL
        );

        $this->addSql(
            <<<SQL
CREATE INDEX members_user_telegram_id_chat_telegram_id
ON members(user_telegram_id, chat_telegram_id);
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
DROP TABLE members;
SQL
        );
    }
}
