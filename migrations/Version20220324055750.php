<?php

declare(strict_types=1);

namespace PenisBot\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220324055750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание таблицы gigachads';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
CREATE TABLE IF NOT EXISTS gigachads (
    id INTEGER PRIMARY KEY,
    count INTEGER,
    user_telegram_id INTEGER NOT NULL,
    chat_telegram_id INTEGER NOT NULL,
    last_update_at DATETIME NOT NULL
);
SQL
        );

        $this->addSql(
            <<<SQL
CREATE INDEX gigachads_user_telegram_id_chat_telegram_id
ON gigachads(user_telegram_id, chat_telegram_id);
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
DROP TABLE pidors;
SQL
        );
    }
}
