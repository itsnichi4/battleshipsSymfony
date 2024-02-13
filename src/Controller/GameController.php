<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Game;
use App\Repository\GameRepository; // Add this line

class GameController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/player/move", name="player_move", methods={"POST"})
     */
    public function playerMove(Request $request)
    {

        $data = json_decode($request->getContent(), true);

        $userId = $data['user'];
        $matchId = $data['matchID'];
        $coordinate = $data['coordinate'];

        $game = $this->entityManager->getRepository(Game::class)->find($matchId);

        if ($game && $game->isValidUser($userId)) {
            $user = $game->getUserById($userId);

            if ($this->validateMove($user, $coordinate)) {
                $opponent = $game->getOpponent($user);

                $this->notifyOpponent($opponent, $matchId, $coordinate);
                $game->addMove($userId, $coordinate);
                $game->updateBoardState($userId, $coordinate);
                $response = [
                    'message' => 'Move successful',
                    'user_board' => $user->getBoardState(),
                    'opponent_board' => $opponent->getBoardState(),
                ];

                return new JsonResponse($response);
            } else {
                $response = ['message' => 'Invalid move'];
            }
        } else {
            $response = ['message' => 'Invalid match or user'];
        }

        return new JsonResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    // Other methods...

    private function validateMove(User $user, $coordinate)
    {
        // Implement your move validation logic here
        // Return true if the move is valid, false otherwise
        // You might need to validate against the user's own moves and opponent's board state
    }

    private function notifyOpponent(User $opponent, $matchId, $coordinate)
    {
        // Implement your notification logic here
        // You might use WebSockets, push notifications, or any other method to notify the opponent
    }
}
