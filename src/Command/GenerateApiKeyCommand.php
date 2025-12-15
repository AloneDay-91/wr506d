<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ApiKeyGenerator;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-api-key',
    description: 'Generate an API key for a user',
)]
class GenerateApiKeyCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ApiKeyGenerator $apiKeyGenerator,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email of the user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        // Find user by email
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user instanceof User) {
            $io->error(sprintf('User with email "%s" not found', $email));
            return Command::FAILURE;
        }

        // Check if user already has an API key
        if (null !== $user->getApiKeyHash()) {
            $io->warning('User already has an API key. It will be revoked and replaced.');
        }

        // Generate new API key
        $keyData = $this->apiKeyGenerator->generate();

        // Store hash and prefix in database
        $user->setApiKeyHash($keyData['hash']);
        $user->setApiKeyPrefix($keyData['prefix']);
        $user->setApiKeyEnabled(true);
        $user->setApiKeyCreatedAt(new DateTimeImmutable());
        $user->setApiKeyLastUsedAt(null);

        $this->entityManager->flush();

        // Display the generated key
        $io->success('API key generated successfully!');
        $io->newLine();
        $io->section('API Key Details');
        $io->text([ sprintf(
            '<comment>User:</comment> %s (%s)',
            $user->getEmail(),
            $user->getFirstname()
            . ' '
            . $user->getLastname()
        ),
            '',
            '<error>⚠️  IMPORTANT: This is the only time you will see the full API key!</error>',
            '<error>   Please copy it and store it securely.</error>',
            '',
            sprintf('<info>Full API Key:</info> %s', $keyData['fullKey']),
            '',
            sprintf('<comment>Prefix:</comment> %s', $keyData['prefix']),
            sprintf('<comment>Status:</comment> Enabled'),
            sprintf('<comment>Created at:</comment> %s', $user->getApiKeyCreatedAt()?->format('Y-m-d H:i:s')),
        ]);
        $io->newLine();

        return Command::SUCCESS;
    }
}
