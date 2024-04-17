<?php

namespace PenisBot\Domain\Actions\Penis;

use PenisBot\Domain\Elements\PenisFromTop;
use PenisBot\Domain\Repositories\PenisesRepository;

class GetTopPenisesAction
{
    private PenisesRepository $penisesRepository;

    public function __construct(PenisesRepository $penisesRepository)
    {
        $this->penisesRepository = $penisesRepository;
    }

    /**
     * @return PenisFromTop[] Пенесы отсортированные по размеру
     */
    public function getTopPenises(int $chatId): array
    {
        return $this->penisesRepository->getPenisOrderBySize($chatId);
    }
}
