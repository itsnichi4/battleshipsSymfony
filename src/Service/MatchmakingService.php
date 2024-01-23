<?php

namespace App\Service;

use App\Repository\MatchmakingRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
class MatchmakingService
{
    private $matchmakingRepository;
    private $entityManager;

    public function __construct(MatchmakingRepository $matchmakingRepository, EntityManagerInterface $entityManager)
    {
        $this->matchmakingRepository = $matchmakingRepository;
        $this->entityManager = $entityManager;
    }

    public function findAvailablePlayers()
    {
        return $this->matchmakingRepository->findAvailablePlayers();
    }

    public function createMatch($player1, $player2)
    {
        // Implement logic to create a match between two players
        // You may need to update the status of players in the Matchmaking entity.
        $player1->setIsAvailable(false);
        $player2->setIsAvailable(false);

        $this->entityManager->persist($player1);
        $this->entityManager->persist($player2);
        $this->entityManager->flush();
    }

    public function findMatch(User $user)
    {
        // Get the list of available players, excluding the current user
        $availablePlayers = $this->findAvailablePlayers();
        $otherPlayers = array_diff($availablePlayers, [$user]);

        // Check if there are available players other than the current user
        if (!empty($otherPlayers)) {
            // Found a match, select the first available player
            $opponent = reset($otherPlayers);

            // Get the User entities for the matched players
            $player1 = $this->entityManager->getRepository(User::class)->find($user->getId());
            $player2 = $this->entityManager->getRepository(User::class)->find($opponent->getId());

            // Create a match between the players
            $this->createMatch($player1, $player2);

            // Remove players from available list (you may need to implement this method in your repository)
            $this->matchmakingRepository->removeAvailablePlayer($player1);
            $this->matchmakingRepository->removeAvailablePlayer($player2);

            return true; // Match found
        }

        // No match found
        return false;
    }


    /**
     * Get ongoing matches.
     *
     * @return array
     */
    public function getOngoingMatches(): array
    {
        // Implement your logic to retrieve ongoing matches
        return $this->matchmakingRepository->findBy(['status' => 'ongoing']);
    }
}
