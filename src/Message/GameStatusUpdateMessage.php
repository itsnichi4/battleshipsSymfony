<?php

namespace App\Message;

class GameStatusUpdateMessage
{
    private $gameId;

    public function __construct(int $gameId)
    {
        $this->gameId = $gameId;
    }

    public function getGameId(): int
    {
        return $this->gameId;
    }
}