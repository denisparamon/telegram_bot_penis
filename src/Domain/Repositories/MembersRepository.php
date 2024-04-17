<?php

namespace PenisBot\Domain\Repositories;

use DateTimeInterface;
use Doctrine\DBAL\Connection;
use PenisBot\Domain\Elements\Member;

class MembersRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getByTelegramIds(int $userTelegramId, int $chatTelegramId): ?Member
    {
        $sql = <<<SQL
SELECT
    id, user_telegram_id, chat_telegram_id, username 
FROM
    members
WHERE
    user_telegram_id = :userTelegramId AND chat_telegram_id = :chatTelegramId
LIMIT 1
SQL;

        $row = $this->connection->fetchAssociative($sql, [
            'userTelegramId' => $userTelegramId,
            'chatTelegramId' => $chatTelegramId,
        ]);

        return !empty($row) ? $this->makeMember($row) : null;
    }

    public function addNewMember(int $userTelegramId, int $chatTelegramId, string $username): void
    {
        $sql = <<<SQL
INSERT INTO members (user_telegram_id, chat_telegram_id, username) VALUES (:userTelegramId, :chatTelegramId, :username)
SQL;

        $this->connection->executeQuery($sql, [
            'userTelegramId' => $userTelegramId,
            'chatTelegramId' => $chatTelegramId,
            'username' => $username,
        ]);
    }

    /**
     * @param int $chatTelegramId
     * @param DateTimeInterface $lastUpdateDateTime
     * @return Member[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function getMembersWithPenisByChatId(int $chatTelegramId, DateTimeInterface $lastUpdateDateTime): array
    {
        $sql = <<<SQL
SELECT
    m.id, m.user_telegram_id, m.chat_telegram_id, m.username 
FROM
    members m
INNER JOIN
    penises p
ON
    p.chat_telegram_id = m.chat_telegram_id AND
    p.user_telegram_id = m.user_telegram_id
LEFT JOIN
    gigachads g
ON
    g.chat_telegram_id = m.chat_telegram_id AND
    g.user_telegram_id = m.user_telegram_id
LEFT JOIN
    pidors pi
ON
    pi.chat_telegram_id = m.chat_telegram_id AND
    pi.user_telegram_id = m.user_telegram_id
WHERE
    m.chat_telegram_id = :chatTelegramId AND
        (
        (
            pi.last_update_at IS NULL OR
            pi.last_update_at < :lastUpdateDateTime
        ) AND
        (
            g.last_update_at IS NULL OR
            g.last_update_at < :lastUpdateDateTime
        )
    )
GROUP BY
    m.id
SQL;

        $rows = $this->connection->fetchAllAssociative($sql, [
            'chatTelegramId' => $chatTelegramId,
            'lastUpdateDateTime' => $lastUpdateDateTime->format('Y-m-d H:i:s'),
        ]);

        $members = [];

        if (empty($rows)) {
            return $members;
        }

        foreach ($rows as $row) {
            $members[] = $this->makeMember($row);
        }

        return $members;
    }

    private function makeMember(array $row): Member
    {
        return new Member(
            (int)$row['id'],
            (int)$row['user_telegram_id'],
            (int)$row['chat_telegram_id'],
            (string)$row['username'],
        );
    }
}
