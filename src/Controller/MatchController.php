<?php
// MatchController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Repository\GameRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class MatchController extends AbstractController
{
    private $gameRepository;
    private $logger;

    public function __construct(GameRepository $gameRepository, LoggerInterface $logger)
    {
        $this->gameRepository = $gameRepository;
        $this->logger = $logger;
    }

    /**
     * @Route("/match/{id}/room", name="app_match_room", requirements={"id"="\d+"})
     */
    public function room($id): Response
    {
        // Fetch the game information based on the provided ID
        $game = $this->gameRepository->find($id);

        // Check if the game exists or is in progress
        if (!$game || $game->getStatus() !== 'pending') {
            throw $this->createNotFoundException('The game does not exist or has already started/finished');
        }

        // Access the players associated with the game
        $player1 = $game->getPlayer1();
        $player2 = $game->getPlayer2();

        // Check if the current user is one of the players
        $currentUser = $this->getUser();
        if (!$currentUser || ($currentUser !== $player1 && $currentUser !== $player2)) {
            throw new AccessDeniedException('Access denied. You are not a participant in this match.');
        }

        return $this->render('match/room.html.twig', [
            'matchId' => $id,
            'player1' => $player1,
            'player2' => $player2,
            'player1Board' => $game->getPlayer1BoardState(),
            'player2Board' => $game->getPlayer2BoardState(),
        ]);
    }

    // Replace this with your actual logic to retrieve player boards
    private function getPlayerBoard($player)
    {
        // Your logic to get the player's board
        // Replace this with your actual implementation
        return [];
    }


    public function click(Request $request, $id)
    {
        $data = json_decode($request->getContent(), true);

        // Extract data from the request
        $userId = $data['userId'];
        $row = $data['row'];
        $column = $data['column'];

        // Implement game state update logic based on the received click event
        // Update the game state, save to the database, and return a JSON response

        return new JsonResponse(['message' => 'Click event handled successfully']);
    }

}