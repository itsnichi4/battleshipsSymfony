<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MatchmakingService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository; // Import the UserRepository

class MultiplayerController extends AbstractController
{
    private $matchmakingService;
    private $userRepository; // Declare UserRepository property

    public function __construct(MatchmakingService $matchmakingService, UserRepository $userRepository)
    {
        $this->matchmakingService = $matchmakingService;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/multiplayer", name="app_multiplayer")
     */
    public function multiplayer(): Response
    {
        // Get the user from the security system
        $user = $this->getUser();

        if (!$user) {
            // Handle the case where the user is not logged in
            // Redirect to the login page or do something else
            return $this->redirectToRoute('app_login');
        }

        // Check for available players and create matches
        $availablePlayers = $this->userRepository->findAvailableUsers();

        if (count($availablePlayers) >= 2) {
            $opponent = array_shift($availablePlayers);
            $this->matchmakingService->createMatch($user, $opponent);
        } else {
            // Update user status or perform other actions
        }

        // Get information about available players and ongoing matches
        $availablePlayers = $this->userRepository->findAvailableUsers();
        $matches = $this->matchmakingService->getOngoingMatches();


        return $this->render('multiplayer/lobby.html.twig', [
            'username' => $user->getUserIdentifier(),
            'availablePlayers' => $availablePlayers,
            'matches' => $matches,
        ]);
    }

    /**
     * @Route("/find-match", name="app_find_match")
     */
    public function findMatch(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            // Handle the case where the user is not logged in
            // Redirect to the login page or do something else
            return $this->redirectToRoute('app_login');
        }

        // Set the user as available for matchmaking
        $user->setIsAvailable(true);

        $entityManager->flush(); // Use the injected EntityManager

        // Redirect back to the multiplayer lobby or render a response
        return $this->redirectToRoute('app_multiplayer');
    }
}
