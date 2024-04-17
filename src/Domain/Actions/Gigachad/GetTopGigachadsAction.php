<?php

namespace PenisBot\Domain\Actions\Gigachad;

use PenisBot\Domain\Elements\GigachadForTop;
use PenisBot\Domain\Repositories\GigachadsRepository;

class GetTopGigachadsAction
{
    private GigachadsRepository $gigachadsRepository;

    public function __construct(GigachadsRepository $gigachadsRepository)
    {
        $this->gigachadsRepository = $gigachadsRepository;
    }

    /**
     * @return GigachadForTop[]
     */
    public function getTopGigachads(int $chatId): array
    {
        return $this->gigachadsRepository->getTopGigachadsOrderByCount($chatId);
    }
}
