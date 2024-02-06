<?php

namespace App\Service;

use App\Repository\MatchmakingRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Game;
use App\Message\GameStatusUpdateMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use App\Repository\GameRepository;

class MatchmakingService
{
    private $matchmakingRepository;
    private $entityManager;
    private $messageBus;
    private $gameRepository;

    public function __construct(
        MatchmakingRepository $matchmakingRepository,
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus,
        GameRepository $gameRepository
    ) {
        $this->matchmakingRepository = $matchmakingRepository;
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
        $this->gameRepository = $gameRepository;
    }

    public function findAvailablePlayers()
    {
        return $this->matchmakingRepository->findAvailablePlayers();
    }

    public function createMatch($player1, $player2)
    {
        // Create a new Game entity
        $game = new Game();
        $game->setExpiresAt(new \DateTime('+9 seconds')); // Set expiration time to 1 seconds from now
        $game->setPlayer1($player1);
        $game->setPlayer2($player2);

        $game->setStatus("pending"); // or whatever default status you want

        // Set players' availability to false
        $player1->setIsAvailable(false);
        $player2->setIsAvailable(false);

        // Persist and flush entities
        $this->entityManager->persist($game);
        $this->entityManager->persist($player1);
        $this->entityManager->persist($player2);
        $this->entityManager->flush();
    }
        public function updateMatchStatus(int $gameId)
        {
            $game = $this->entityManager->getRepository(Game::class)->find($gameId);
    
            if ($game && $game->getStatus() === 'pending') {
                $game->setStatus('finished');
    
                // Calculate the delay timestamp
                $delayTimestamp = $game->getExpiresAt()->getTimestamp();
    
                // Create a DelayStamp with the calculated delay timestamp
                $delayStamp = new DelayStamp($delayTimestamp);
    
                // Dispatch the message with the game ID for status update and the DelayStamp
                $this->messageBus->dispatch(new GameStatusUpdateMessage($game->getId()), [$delayStamp]);
            }
            }

            public function fetchPendingMatches()
            {
                return $this->gameRepository->findPendingMatches();
            }

}
