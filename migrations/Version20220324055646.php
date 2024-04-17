<?php

declare(strict_types=1);

namespace PenisBot\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220324055646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание таблицы pidors';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
CREATE TABLE IF NOT EXISTS pidors (
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
CREATE INDEX pidors_user_telegram_id_chat_telegram_id
ON pidors(user_telegram_id, chat_telegram_id);
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
