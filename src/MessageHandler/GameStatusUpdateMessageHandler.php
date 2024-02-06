<?php

// src/MessageHandler/GameStatusUpdateMessageHandler.php

namespace App\MessageHandler;

use App\Entity\Game;
use App\Message\GameStatusUpdateMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GameStatusUpdateMessageHandler implements MessageHandlerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(GameStatusUpdateMessage $message)
    {
  

        $game = $this->entityManager->getRepository(Game::class)->find($message->getGameId());

        dump('Game ID: ' . $game->getId());
        dump('Current Time: ' . (new \DateTime())->format('Y-m-d H:i:s'));
        dump('Expires At: ' . $game->getExpiresAt()->format('Y-m-d H:i:s'));
        
        if ($game->getStatus() === 'pending') {
            $game->setStatus('finished');
        
            
            // Persist and flush the changes
            $this->entityManager->persist($game);
            $this->entityManager->flush();
        }
    }
}
