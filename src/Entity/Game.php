<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="player1_id", referencedColumnName="id", nullable=false)
     */
    private $player1;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="player2_id", referencedColumnName="id", nullable=true)
     */
    private $player2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $hitsPlayer1;

    /**
     * @ORM\Column(type="integer")
     */
    private $missesPlayer1;

    /**
     * @ORM\Column(type="integer")
     */
    private $hitsPlayer2;

    /**
     * @ORM\Column(type="integer")
     */
    private $missesPlayer2;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $player1BoardState;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $player2BoardState;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $player1Moves;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $player2Moves;


    /**
     * @ORM\Column(type="datetime")
     */
    private $expiresAt;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $moves;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $currentPlayer;

    /**
     * Add a move to the game
     *
     * @param int $playerId
     * @param array $coordinate
     */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer1(): ?User
    {
        return $this->player1;
    }

    public function setPlayer1(User $player1): self
    {
        $this->player1 = $player1;

        return $this;
    }

    public function getPlayer2(): ?User
    {
        return $this->player2;
    }

    public function setPlayer2(User $player2): self
    {
        $this->player2 = $player2;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getHitsPlayer1(): ?int
    {
        return $this->hitsPlayer1;
    }

    public function setHitsPlayer1(int $hitsPlayer1): self
    {
        $this->hitsPlayer1 = $hitsPlayer1;

        return $this;
    }

    public function getMissesPlayer1(): ?int
    {
        return $this->missesPlayer1;
    }

    public function setMissesPlayer1(int $missesPlayer1): self
    {
        $this->missesPlayer1 = $missesPlayer1;

        return $this;
    }

    public function getHitsPlayer2(): ?int
    {
        return $this->hitsPlayer2;
    }

    public function setHitsPlayer2(int $hitsPlayer2): self
    {
        $this->hitsPlayer2 = $hitsPlayer2;

        return $this;
    }

    public function getMissesPlayer2(): ?int
    {
        return $this->missesPlayer2;
    }

    public function setMissesPlayer2(int $missesPlayer2): self
    {
        $this->missesPlayer2 = $missesPlayer2;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    public function getPlayer1BoardState(): ?array
    {
        return $this->player1BoardState;
    }

    public function setPlayer1BoardState(array $player1BoardState): self
    {
        $this->player1BoardState = $player1BoardState;

        return $this;
    }

    public function getPlayer2BoardState(): ?array
    {
        return $this->player2BoardState;
    }

    public function setPlayer2BoardState(array $player2BoardState): self
    {
        $this->player2BoardState = $player2BoardState;

        return $this;
    }

    public function getPlayer1Moves(): ?array
    {
        return $this->player1Moves;
    }

    public function setPlayer1Moves(array $player1Moves): self
    {
        $this->player1Moves = $player1Moves;

        return $this;
    }

    public function getPlayer2Moves(): ?array
    {
        return $this->player2Moves;
    }

    public function setPlayer2Moves(array $player2Moves): self
    {
        $this->player2Moves = $player2Moves;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        // Call checkExpiration method when setting expiresAt
        $this->checkExpiration();

        return $this;
    }

    private function checkExpiration(): void
    {
        if ($this->status === 'pending' && new \DateTime() >= $this->expiresAt) {
            // The game has expired, update the status to "finished"
            $this->status = 'finished';
        }
    }

    public function getPlayerById($playerId): ?User
    {
        return $playerId === $this->getPlayer1()->getId() ? $this->getPlayer1() : ($playerId === $this->getPlayer2()->getId() ? $this->getPlayer2() : null);
    }
    
    private function initializeEmptyBoard(): array
    {
        $board = [];

        $letters = range('A', 'J');

        for ($row = 0; $row < 10; $row++) {
            for ($col = 1; $col <= 10; $col++) {
                $board[$letters[$row]][$col] = [
                    'x' => $col,
                    'y' => $letters[$row],
                    'status' => 'unclicked',
                    'ship' => null, // Add a 'ship' key to store information about the placed ship
                ];
            }
        }

        $ships = [
            ['name' => 'Carrier', 'size' => 5],
            ['name' => 'Battleship', 'size' => 4],
            ['name' => 'Cruiser', 'size' => 3],
            ['name' => 'Submarine', 'size' => 3],
            ['name' => 'Destroyer', 'size' => 2],
        ];

        foreach ($ships as $ship) {
            $this->placeShipRandomly($board, $ship['name'], $ship['size']);
        }

        return $board;
    }

    private function placeShipRandomly(array &$board, string $shipName, int $shipSize): void
    {
        // Implement logic to randomly place the ship on the board
        // You need to ensure that the ship doesn't overlap with other ships

        $letters = range('A', 'J');

        do {
            $orientation = rand(0, 1); // 0 for horizontal, 1 for vertical

            if ($orientation == 0) {
                $maxRow = 10;
                $maxCol = 11 - $shipSize;
            } else {
                $maxRow = 11 - $shipSize;
                $maxCol = 10;
            }

            $row = rand(0, $maxRow - 1);
            $col = rand(1, $maxCol);

            $validPlacement = true;

            for ($i = 0; $i < $shipSize; $i++) {
                $rowIndex = $row + ($orientation == 1 ? $i : 0);
                $colIndex = $col + ($orientation == 0 ? $i : 0);

                if (!isset($letters[$rowIndex]) || !isset($board[$letters[$rowIndex]][$colIndex])) {
                    // Invalid indices, retry placement
                    $validPlacement = false;
                    break;
                }

                $cell = $board[$letters[$rowIndex]][$colIndex];

                if ($cell['ship'] !== null) {
                    // Ship overlap, retry placement
                    $validPlacement = false;
                    break;
                }
            }
        } while (!$validPlacement);

        // Place the ship on the board
        for ($i = 0; $i < $shipSize; $i++) {
            $rowIndex = $row + ($orientation == 1 ? $i : 0);
            $colIndex = $col + ($orientation == 0 ? $i : 0);
            $board[$letters[$rowIndex]][$colIndex]['ship'] = $shipName;
        }
    }


    public function isValidUser(int $userId): bool
    {
        return $this->getPlayer1()->getId() === $userId || $this->getPlayer2()->getId() === $userId;
        // Adjust the logic based on your actual user identification in the game entity.
    }

    public function __construct()
    {
        $this->hitsPlayer1 = 0;
        $this->missesPlayer1 = 0;
        $this->hitsPlayer2 = 0;
        $this->missesPlayer2 = 0;
        $this->createdAt = new \DateTime(); // Assuming you want the creation date to be set to the current date and time by default

        // Set expiration time to 30 seconds from the creation date
        $expiresAt = new \DateTime();
        $expiresAt->modify('+30 seconds');
        $this->expiresAt = $expiresAt;

        // Set the initial status to "pending"
        $this->status = 'pending';

        // Assuming your board state is an empty 10x10 board represented by an array of arrays
        $this->player1BoardState = $this->initializeEmptyBoard();
        $this->player2BoardState = $this->initializeEmptyBoard();

        $this->player1Moves = [];
        $this->player2Moves = [];

        // Check if the game has expired during construction
        $this->checkExpiration();
    }



    public function getCurrentPlayer(): int
    {
        return $this->currentPlayer;
    }

    public function setCurrentPlayer(int $currentPlayer): self
    {
        $this->currentPlayer = $currentPlayer;

        return $this;
    }

/**
 * Add a move to the game
 *
 * @param int $playerId
 * @param array $coordinate
 */
public function addMove(int $playerId, array $coordinate): void
{
    // Implement logic to add a move to the game
    // You can format the move data and append it to the $moves array
    $this->moves[] = [
        'player' => $playerId,
        'coordinate' => $coordinate,
        'timestamp' => new \DateTime(),
    ];

    // Toggle the current player for the next move
    $this->currentPlayer = ($this->currentPlayer === 1) ? 2 : 1;
}

/**
 * Update the board state based on the opponent's move
 *
 * @param int $opponentId
 * @param array $coordinate
 */
// Update the board state based on the opponent's move
public function updateBoardState(int $opponentId, array $coordinate): void
{
    // Implement your board state update logic here
    // You can use the $coordinate array to determine the impact of the move on the board
    // Update the board state based on the opponent's move

    $letters = range('A', 'J');
    $rowIndex = array_search($coordinate['row'], $letters);
    $colIndex = $coordinate['column'];

    // Use an associative array to store board states for both players
    $playerBoardStates = [
        1 => &$this->player1BoardState,
        2 => &$this->player2BoardState,
    ];

    // Check if the clicked cell is a valid coordinate
    if (isset($letters[$rowIndex]) && isset($playerBoardStates[$opponentId][$letters[$rowIndex]][$colIndex])) {
        // Update the status of the clicked cell to 'clicked'
        $playerBoardStates[$opponentId][$letters[$rowIndex]][$colIndex]['status'] = 'clicked';
        error_log('updateBoardState method called');
        error_log('Row Index: ' . $rowIndex);
        error_log('Col Index: ' . $colIndex);
        // No need to check for opponentId, use it directly to access the correct player's board state
    }
}


    
}
