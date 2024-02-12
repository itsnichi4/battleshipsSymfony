<?php

// src/Repository/WebSocketConnectionRepository.php

namespace App\Repository;

use Ratchet\ConnectionInterface;

class WebSocketConnectionRepository
{
    private $connections = [];

    public function addConnection(ConnectionInterface $connection): void
    {
        $this->connections[] = $connection;
    }

    public function removeConnection(ConnectionInterface $connection): void
    {
        $index = array_search($connection, $this->connections);
        if ($index !== false) {
            unset($this->connections[$index]);
        }
    }

    /**
     * @return ConnectionInterface[]
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    public function broadcastMessage(string $message): void
    {
        foreach ($this->connections as $connection) {
            $connection->send($message);
        }
    }
}
