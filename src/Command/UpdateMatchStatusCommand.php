<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Entity\Game;
use App\Message\GameStatusUpdateMessage;
use App\Service\MatchmakingService;

class UpdateMatchStatusCommand extends Command
{
    private $messageBus;
    private $matchmakingService;

    public function __construct(MessageBusInterface $messageBus, MatchmakingService $matchmakingService)
    {
        parent::__construct();

        $this->messageBus = $messageBus;
        $this->matchmakingService = $matchmakingService;
    }

    protected function configure()
    {
        $this
            ->setName('app:update-match-status')
            ->setDescription('Update match status and dispatch messages with a delay');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Fetch pending matches
        $matches = $this->matchmakingService->fetchPendingMatches();

        foreach ($matches as $match) {
            $matchId = $match->getId();

            // Update match status to "finished"
            $this->matchmakingService->updateMatchStatus($matchId);

            // Dispatch a message with a delay
            $delay = 10; // seconds
            $message = new GameStatusUpdateMessage($matchId, $delay);
            $this->messageBus->dispatch($message);
        }

        return Command::SUCCESS;
    }
}
