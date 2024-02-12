<?php

namespace App\Service;

use Exception;
use Ratchet\ConnectionInterface;
use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use App\Repository\WebSocketConnectionRepository;

class GameLogicService



{

    private $entityManager;
    private $logger;
    private $webSocketConnectionRepository; // Add the repository as a property

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager, WebSocketConnectionRepository $webSocketConnectionRepository)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->webSocketConnectionRepository = $webSocketConnectionRepository; // Inject the repository
    }

    private function getPlayersFromGameId(int $gameId): ?array
    {
        $game = $this->entityManager->getRepository(Game::class)->find($gameId);

        if (!$game) {
            return null;
        }

        $player1 = $game->getPlayer1();
        $player2 = $game->getPlayer2();

        return [$player1, $player2];
    }

    public function handleMove(ConnectionInterface $playerConnection, array $data): void
    {
        try {
            $this->logger->info('Received WebSocket messageaaaa', ['payload' => json_encode($data)]);

            // Get the game ID and player ID from the incoming data

            $gameId = $data['gameId'];
            $playerId = $data['playerId'];
            $players = $this->getPlayersFromGameId($gameId);

            $this->logger->info('Game ID: ' . $gameId);
            $this->logger->info('Player ID: ' . $playerId);

            // Retrieve the game entity from the database
            $game = $this->entityManager->getRepository(Game::class)->find($gameId);

            if ($game && $game->isValidUser($playerId)) {
                $user = $game->getPlayerById($playerId);


                $this->logger->info('User: ' . json_encode($user));

                // Extract coordinate data from the incoming message
                $row = $data['row'];
                $column = $data['column'];

                $this->logger->info('Received move data: ' . json_encode($data));  // Log the received move data

                // Validate the move
                if ($players && $this->validateMove($game, $user, $row, $column)) {
                    [$player1, $player2] = $players;
                    $opponent = ($user === $player1) ? $player2 : $player1;

                    $this->logger->info('Opponent: ' . json_encode($opponent));



                    // Update the game state
                    $game->addMove($playerId, ['row' => $row, 'column' => $column]);
                    $game->updateBoardState($playerId, ['row' => $row, 'column' => $column]);
                    $this->entityManager->flush();

                    // // Send a response to the player who made the move
                    // $this->sendResponse($playerConnection, [
                    //     'type' => 'move_response',
                    //     'message' => 'Move successful',
                    //     'user_board' => $user->getBoardState(),
                    //     'opponent_board' => $opponent->getBoardState(),
                    // ]);

                    $this->logger->info('Move processed successfully.');  // Log that the move was processed successfully
                } else {
                    // Send an invalid move response to the player
                    $this->sendResponse($playerConnection, [
                        'type' => 'move_response',
                        'message' => 'Invalid move',
                    ]);

                    $this->logger->info('Invalid move received.');  // Log that an invalid move was received
                }
            } else {
                $this->logger->info('Game or user validation failed.');  // Log that game or user validation failed
            }
        } catch (Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage(), ['exception' => $e]);
        }
    }



    private function validateMove()
    {
        // Implement your move validation logic here
        // Return true if the move is valid, false otherwise
        // You might need to validate against the user's own moves and opponent's board state

        // Example validation:
        return true;
    }
    //TODO: validatemove logic





    public function onOpen(ConnectionInterface $connection): void
    {
        // Add the new connection to the repository
        $this->webSocketConnectionRepository->addConnection($connection);
    }

    public function onClose(ConnectionInterface $connection): void
    {
        // Remove the closed connection from the repository
        $this->webSocketConnectionRepository->removeConnection($connection);
    }

    private function sendResponse(ConnectionInterface $connection, array $response): void
    {
        // Send the JSON-encoded response to the specified WebSocket connection
        $connection->send(json_encode($response));
    }
}