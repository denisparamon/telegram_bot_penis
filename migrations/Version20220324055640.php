<?php

declare(strict_types=1);

namespace PenisBot\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220324055640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание таблицы penises';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
CREATE TABLE IF NOT EXISTS penises (
    id INTEGER PRIMARY KEY,
    size INTEGER NOT NULL,
    user_telegram_id INTEGER NOT NULL,
    chat_telegram_id INTEGER NOT NULL,
    last_update_at DATETIME NOT NULL
);
SQL
        );

        $this->addSql(
            <<<SQL
CREATE INDEX penises_user_telegram_id_chat_telegram_id
ON penises(user_telegram_id, chat_telegram_id);
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
DROP TABLE penises;
SQL
        );
    }
}
