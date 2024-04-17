<?php

namespace PenisBot\Domain\Repositories;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Connection;
use PenisBot\Domain\Elements\Member;
use PenisBot\Domain\Elements\Penis;
use PenisBot\Domain\Elements\PenisFromTop;

class PenisesRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getPenisByMember(Member $member): ?Penis
    {
        $sql = <<<SQL
SELECT
    id, size, last_update_at
FROM
    penises
WHERE
    user_telegram_id = :userTelegramId AND chat_telegram_id = :chatTelegramId
LIMIT 1
SQL;

        $row = $this->connection->fetchAssociative($sql, [
            'userTelegramId' => $member->getUserTelegramId(),
            'chatTelegramId' => $member->getChatTelegramId(),
        ]);

        return !empty($row) ? $this->makePenis($member, $row) : null;
    }

    public function addNewPenis(Member $member, int $size, DateTimeInterface $lastUpdateAt): void
    {
        $sql = <<<SQL
INSERT INTO penises
    (size, user_telegram_id, chat_telegram_id, last_update_at)
VALUES
    (:size, :userTelegramId, :chatTelegramId, :lastUpdateAt)
SQL;

        $this->connection->executeQuery($sql, [
            'size' => $size,
            'userTelegramId' => $member->getUserTelegramId(),
            'chatTelegramId' => $member->getChatTelegramId(),
            'lastUpdateAt' => $lastUpdateAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function updatePenis(Penis $penis, int $size, bool $needUpdateLastUpdateDate): void
    {
        $sql = <<<SQL
UPDATE penises SET
    size = :size,
    last_update_at = :lastUpdateAt
WHERE
    id = :id 
SQL;
        $args = [
            'size' => $size,
            'id' => $penis->getId(),
        ];

        if ($needUpdateLastUpdateDate) {
            $args['lastUpdateAt'] = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        } else {
            $args['lastUpdateAt'] = $penis->getLastUpdateAt()->format('Y-m-d H:i:s');
        }

        $this->connection->executeQuery($sql, $args);
    }

    /**
     * @return PenisFromTop[]
     */
    public function getPenisOrderBySize(int $chatId): array
    {
        $sql = <<<SQL
SELECT
    p.id, p.size, m.username
FROM
    penises p
INNER JOIN
    members m
ON
    p.chat_telegram_id = m.chat_telegram_id AND
    p.user_telegram_id = m.user_telegram_id
WHERE
    p.chat_telegram_id = :chatTelegramId
ORDER BY
    size DESC
SQL;

        $rows = $this->connection->fetchAllAssociative($sql, [
            'chatTelegramId' => $chatId,
        ]);

        $penises = [];

        if (empty($rows)) {
            return $penises;
        }

        foreach ($rows as $row) {
            $penises[] = $this->makePenisFromTop($row);
        }

        return $penises;
    }

    private function makePenis(Member $member, array $row): Penis
    {
        return new Penis(
            (int)$row['id'],
            (int)$row['size'],
            new DateTimeImmutable((string)$row['last_update_at']),
            $member
        );
    }

    private function makePenisFromTop(array $row): PenisFromTop
    {
        return new PenisFromTop(
            (int)$row['id'],
            (int)$row['size'],
            (string)$row['username'],
        );
    }
}
