<?php

namespace App\Command;

use App\Repository\UserRepository;
use App\Application\Subscription\GrantManualSubscription;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
  name: 'app:grant-premium',
  description: 'Grant manual premium subscription to a user'
)]
final class GrantPremiumCommand extends Command
{
  public function __construct(
    private UserRepository $userRepository,
    private GrantManualSubscription $grantManualSubscription,
  ) {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this
      ->addArgument('email', InputArgument::REQUIRED, 'User email')
      ->addArgument('months', InputArgument::OPTIONAL, 'Duration in months', 12);
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $email = $input->getArgument('email');
    $months = (int) $input->getArgument('months');

    $user = $this->userRepository->findOneBy(['email' => $email]);

    if (!$user) {
      $output->writeln('<error>User not found</error>');
      return Command::FAILURE;
    }

    $subscription = $this->grantManualSubscription->execute($user, $months);

    $output->writeln(sprintf(
      '<info>Premium granted until %s</info>',
      $subscription->getExpiresAt()->format('Y-m-d')
    ));

    return Command::SUCCESS;
  }
}
