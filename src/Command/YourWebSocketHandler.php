<?php

// src/WebSocket/YourWebSocketHandler.php

namespace App\WebSocket;

use Exception;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;

class YourWebSocketHandler implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ($conn->resourceId)\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Broadcast the message to all other clients
        foreach ($this->clients as $client) {
            if ($client !== $from) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send messages to it
        $this->clients->detach($conn);

        echo "Connection $conn->resourceId has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo "An error occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
