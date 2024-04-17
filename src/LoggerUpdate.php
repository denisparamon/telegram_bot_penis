<?php

namespace PenisBot;

use DateTimeImmutable;
use DateTimeInterface;
use Telegram\Bot\Objects\Update;

class LoggerUpdate
{
    private string $pathToLogDir;
    private DateTimeInterface $currentDateTime;

    public function __construct(string $pathToLogDir)
    {
        $this->currentDateTime = new DateTimeImmutable();

        $pathToLogDir .= '/updates/' . $this->currentDateTime->format('Y_m_d');
        $this->tryMakeDir($pathToLogDir);

        $this->pathToLogDir = $pathToLogDir;
    }

    public function log(Update $update, string $rawUpdate): void
    {
        $rawUpdatePretty = json_encode(json_decode($rawUpdate, true), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $pathToLogFile = $this->pathToLogDir . '/' . sprintf(
                '%s_update_%s.log',
                $this->currentDateTime->format('H_i_s'),
                $update->getUpdateId()
            );
        $content = $rawUpdatePretty . PHP_EOL . PHP_EOL . print_r($update, true);

        file_put_contents($pathToLogFile, $content, FILE_APPEND);
    }

    private function tryMakeDir(string $pathToLogDir): void
    {
        if (is_dir($pathToLogDir)) {
            return;
        }

        if (!mkdir($pathToLogDir, 0777, true) && !is_dir($pathToLogDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $pathToLogDir));
        }
    }
}
