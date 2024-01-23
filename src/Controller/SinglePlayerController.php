<?php

// src/Controller/SinglePlayerController.php



namespace App\Controller;

use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SinglePlayerController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/single-player", name="single_player")
     */
    public function index(): Response
    {
        // Assuming you have logic to retrieve the current user, replace this with your actual user retrieval logic
        $currentUser = $this->getUser();

        // Create a new game
        $game = $this->createGame($currentUser);
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        // Determine the current player's turn (assuming 'player1' starts first)
        $currentPlayer = $game->getPlayer1()->getUsername();

        return $this->render('single_player/index.html.twig', [
            'message' => 'New game created!',
            'game' => $game,
            'currentPlayer' => $currentPlayer,
        ]);
    }

    /**
     * Create a new game.
     */
    private function createGame($currentUser): Game
    {
        $game = new Game();
        $game->setPlayer1($currentUser);
        $game->setStatus('pending');
        $game->setCreatedAt(new \DateTime());
    
        // Initialize player and AI board states (empty for now)
        $game->setPlayer1BoardState($this->initializeEmptyBoard());
        $game->setPlayer2BoardState($this->initializeEmptyBoard());
    
        // Initialize player and AI moves
        $game->setPlayer1Moves([]);
        $game->setPlayer2Moves([]);
    
        // Initialize hits and misses to 0
        $game->setHitsPlayer1(0);
        $game->setMissesPlayer1(0);
        $game->setHitsPlayer2(0);
        $game->setMissesPlayer2(0);
    
        return $game;
    }
    
    
    

    /**
     * Initialize an empty 10x10 board.
     */
    private function initializeEmptyBoard(): array
    {
        $board = [];
        for ($i = 0; $i < 10; $i++) {
            $board[] = array_fill(0, 10, '.');
        }
        return $board;
    }
}
