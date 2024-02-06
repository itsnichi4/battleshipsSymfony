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

    private function initializeEmptyBoard(): array
    {
        $board = [];

        $letters = range('A', 'J');

        for ($row = 0; $row < 10; $row++) {
            for ($col = 1; $col <= 10; $col++) {
                $board[$letters[$row]][$col] = '.';
            }
        }

        return $board;
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

    
}
