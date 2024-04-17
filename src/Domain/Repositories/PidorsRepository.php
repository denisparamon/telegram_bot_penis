<?php

namespace PenisBot\Domain\Repositories;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use PenisBot\Domain\Elements\PidorForTop;

class PidorsRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getCountPidor(int $userTelegramId, int $chatTelegramId): ?int
    {
        $sql = <<<SQL
SELECT
    count
FROM
    pidors
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

    public function addPidor(int $userTelegramId, int $chatTelegramId, int $count): void
    {
        $sql = <<<SQL
INSERT INTO pidors
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

    public function updatePidor(int $userTelegramId, int $chatTelegramId, int $count): void
    {
        $sql = <<<SQL
UPDATE pidors SET
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

    public function getLastPidorDateTime(int $chatTelegramId): ?DateTimeImmutable
    {
        $sql = <<<SQL
SELECT
    last_update_at
FROM
    pidors
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

    public function getLastPidorUserTelegramId(int $chatTelegramId): int
    {
        $sql = <<<SQL
SELECT
    user_telegram_id
FROM
    pidors
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

    public function getTopPidorsOrderByCount(int $chatTelegramId): array
    {
        $sql = <<<SQL
SELECT
    p.count, m.username
FROM
    pidors p
INNER JOIN
    members m
ON
    p.chat_telegram_id = m.chat_telegram_id AND
    p.user_telegram_id = m.user_telegram_id
WHERE
    p.chat_telegram_id = :chatTelegramId
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

    private function makePidorsForTop(array $row): PidorForTop
    {
        return new PidorForTop(
            (int)$row['count'],
            (string)$row['username'],
        );
    }
}
