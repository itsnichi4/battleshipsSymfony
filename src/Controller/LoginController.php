<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Service\MatchmakingService;

class LoginController extends AbstractController
{
    private $matchmakingService;

    public function __construct(MatchmakingService $matchmakingService)
    {
        $this->matchmakingService = $matchmakingService;
    }

    /**
     * @Route("/login", name="app_login", methods={"GET", "POST"})
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_main_menu');
        }
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        //dd($error,$lastUsername);
        return $this->render('login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        // This controller will not be executed,
        // as the route is handled by the security system.
        // You can add a return statement with a redirect if needed.
    }

    /**
     * @Route("/main-menu", name="app_main_menu")
     */
    public function mainMenu(): Response
    {
        // Get the user from the security system
        $user = $this->getUser();

        if (!$user) {
            // Handle the case where the user is not logged in
            // Redirect to the login page or do something else
            return $this->redirectToRoute('app_login');
        }

        // Render the main menu
        return $this->render('main_menu.html.twig', [
            'username' => $user->getUserIdentifier(),
        ]);
    }


}
