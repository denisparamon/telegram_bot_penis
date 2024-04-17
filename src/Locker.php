<?php

namespace PenisBot;

use DateTimeImmutable;

class Locker
{
    public function issetLock(string $key): bool
    {
        $hash = md5('penis-bot-locker' . $key);

        if (file_exists('/tmp/' . $hash) === false) {
            return false;
        }

        $content = file_get_contents('/tmp/' . $hash);

        $diff = (new DateTimeImmutable($content))->diff(new DateTimeImmutable());

        // Если блокирующий файл живет дольше 5 секунд
        if ($diff->d > 0 || $diff->h > 0 || $diff->i > 0|| $diff->s > 5) {
            $this->unlock($key);
            return false;
        }

        return true;
   }

    public function lock($key): void
    {
        $hash = md5('penis-bot-locker' . $key);

        file_put_contents('/tmp/' . $hash, (new DateTimeImmutable())->format('Y-m-d H:i:s'));
   }

    public function unlock($key): void
    {
        $hash = md5('penis-bot-locker' . $key);

        unlink('/tmp/' . $hash);
   }
}
