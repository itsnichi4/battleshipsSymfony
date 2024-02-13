<?php

namespace App\Command;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use SplObjectStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;
use App\Service\GameLogicService;


class BattleshipsWebsocketCommand extends Command implements MessageComponentInterface
{
    protected static $defaultName = 'app:battleships-websocket';

    protected $clients;
    protected $gameLogicService;

    protected $logger;

    public function __construct(LoggerInterface $logger, GameLogicService $gameLogicService)
    {
        $this->clients = new SplObjectStorage();
        $this->logger = $logger;
        $this->gameLogicService = $gameLogicService; // Add this line to inject the GameLogicService
        parent::__construct(); // Ensure to call the parent constructor
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer($this)
            ),
            8080 // Set the port you want to use for WebSocket connections
        );

        $output->writeln('WebSocket server running on port 8080.');

        $server->run();

        return Command::SUCCESS;
    }

    // Implement the MessageComponentInterface methods
    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection
        $this->clients->attach($conn);
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Remove the connection when it's closed
        $this->clients->detach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {

        // Parse the incoming message (assuming it's in JSON format)
        $data = json_decode($msg, true);

        // Log the received payload
        $this->logger->info('Received WebSocket message', ['payload' => $data]);

        // Perform game logic based on the received data
        $this->gameLogicService->handleMove($from, $data);

        // Notify opponents about the move and updated game state
        $this->notifyPlayers($data);
    }



    // Notify all connected clients except the sender
    private function notifyPlayers(array $data)
    {
        foreach ($this->clients as $client) {

            $client->send(json_encode([
                'type' => 'move_notification',
                'data' => $data,
            ]));

        }
    }




    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        // Handle WebSocket errors
    }
}
