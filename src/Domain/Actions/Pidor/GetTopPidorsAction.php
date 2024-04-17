<?php

namespace PenisBot\Domain\Actions\Pidor;

use PenisBot\Domain\Elements\PidorForTop;
use PenisBot\Domain\Repositories\PidorsRepository;

class GetTopPidorsAction
{
    private PidorsRepository $pidorsRepository;

    public function __construct(PidorsRepository $pidorsRepository)
    {
        $this->pidorsRepository = $pidorsRepository;
    }

    /**
     * @return PidorForTop[]
     */
    public function getTopPidors(int $chatId): array
    {
        return $this->pidorsRepository->getTopPidorsOrderByCount($chatId);
    }
}
