<?php

namespace App\Message;

class GameStatusUpdateMessage
{
    private $gameId;
    private $delay;

    public function __construct(int $gameId, int $delay)
    {
        $this->gameId = $gameId;
        $this->delay = $delay;
    }

    public function getGameId(): int
    {
        return $this->gameId;
    }

    public function getDelay(): int
    {
        return $this->delay;
    }
}