<?php

namespace PenisBot\Domain\Repositories;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use PenisBot\Domain\Elements\GigachadForTop;

class GigachadsRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getCountGigachad(int $userTelegramId, int $chatTelegramId): ?int
    {
        $sql = <<<SQL
SELECT
    count
FROM
    gigachads
WHERE
    user_telegram_id = :userTelegramId AND chat_telegram_id = :chatTelegramId
LIMIT 1
SQL;

        $row = $this->connection->fetchAssociative($sql, [
            'userTelegramId' => $userTelegramId,
            'chatTelegramId' => $chatTelegramId,
        ]);

        return !empty($row) ? (int)$row['count'] : null;
    }

    public function addGigachad(int $userTelegramId, int $chatTelegramId, int $count): void
    {
        $sql = <<<SQL
INSERT INTO gigachads
    (count, user_telegram_id, chat_telegram_id, last_update_at)
VALUES
    (:count, :userTelegramId, :chatTelegramId, :lastUpdateAt)
SQL;

        $this->connection->executeQuery($sql, [
            'count' => $count,
            'userTelegramId' => $userTelegramId,
            'chatTelegramId' => $chatTelegramId,
            'lastUpdateAt' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);
    }

    public function updateGigachad(int $userTelegramId, int $chatTelegramId, int $count): void
    {
        $sql = <<<SQL
UPDATE gigachads SET
    count = :count,
    last_update_at = :lastUpdateAt
WHERE
    user_telegram_id = :userTelegramId AND
    chat_telegram_id = :chatTelegramId
SQL;

        $this->connection->executeQuery($sql, [
            'count' => $count,
            'lastUpdateAt' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            'userTelegramId' => $userTelegramId,
            'chatTelegramId' => $chatTelegramId,
        ]);
    }

    public function getLastGigachadDateTime(int $chatTelegramId): ?DateTimeImmutable
    {
        $sql = <<<SQL
SELECT
    last_update_at
FROM
    gigachads
WHERE
    chat_telegram_id = :chatTelegramId
ORDER BY
    last_update_at DESC
LIMIT 1
SQL;

        $row = $this->connection->fetchAssociative($sql, [
            'chatTelegramId' => $chatTelegramId,
        ]);

        if (empty($row)) {
            return null;
        }

        return new DateTimeImmutable((string)$row['last_update_at']);
    }

    public function getLastGigachadUserTelegramId(int $chatTelegramId): int
    {
        $sql = <<<SQL
SELECT
    user_telegram_id
FROM
    gigachads
WHERE
    chat_telegram_id = :chatTelegramId
ORDER BY
    last_update_at DESC
LIMIT 1
SQL;

        $row = $this->connection->fetchAssociative($sql, [
            'chatTelegramId' => $chatTelegramId,
        ]);

        return (int)$row['user_telegram_id'];
    }

    public function getTopGigachadsOrderByCount(int $chatTelegramId): array
    {
        $sql = <<<SQL
SELECT
    g.count, m.username
FROM
    gigachads g
INNER JOIN
    members m
ON
    g.chat_telegram_id = m.chat_telegram_id AND
    g.user_telegram_id = m.user_telegram_id
WHERE
    g.chat_telegram_id = :chatTelegramId
ORDER BY
    count DESC
SQL;

        $rows = $this->connection->fetchAllAssociative($sql, [
            'chatTelegramId' => $chatTelegramId,
        ]);

        $pidors = [];

        if (empty($rows)) {
            return $pidors;
        }

        foreach ($rows as $row) {
            $pidors[] = $this->makePidorsForTop($row);
        }

        return $pidors;
    }

    private function makePidorsForTop(array $row): GigachadForTop
    {
        return new GigachadForTop(
            (int)$row['count'],
            (string)$row['username'],
        );
    }
}
