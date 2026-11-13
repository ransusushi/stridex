<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-user')]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private KernelInterface $kernel,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ('prod' === $this->kernel->getEnvironment()) {
            $output->writeln('<error>This development account command is disabled in production.</error>');

            return Command::FAILURE;
        }

        $io = new SymfonyStyle($input, $output);
        if (!$input->isInteractive()) {
            $io->error('Run this command interactively to enter account passwords securely.');

            return Command::FAILURE;
        }

        $adminPassword = $this->askPassword($io, 'Admin');
        if (null === $adminPassword) {
            return Command::FAILURE;
        }

        $staffPassword = $this->askPassword($io, 'Staff');
        if (null === $staffPassword) {
            return Command::FAILURE;
        }

        // 1. Setup Admin
        $admin = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@roa.com']);
        if (!$admin) {
            $admin = new User();
            $admin->setEmail('admin@roa.com');
        }
        $admin->setName('System Admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setStatus('active');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, $adminPassword));
        $this->entityManager->persist($admin);

        // 2. Setup Staff
        $staff = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'staff@roa.com']);
        if (!$staff) {
            $staff = new User();
            $staff->setEmail('staff@roa.com');
        }
        $staff->setName('Staff Member');
        $staff->setRoles(['ROLE_STAFF']);
        $staff->setStatus('active');
        $staff->setPassword($this->passwordHasher->hashPassword($staff, $staffPassword));
        $this->entityManager->persist($staff);

        // Save to database
        $this->entityManager->flush();

        $io->success('ROA admin and staff accounts are ready. Passwords were not displayed.');

        return Command::SUCCESS;
    }

    private function askPassword(SymfonyStyle $io, string $role): ?string
    {
        $validator = static function (?string $password): string {
            if (null === $password || \strlen($password) < 8) {
                throw new \RuntimeException('Password must be at least 8 characters long.');
            }

            return $password;
        };

        $password = $io->askHidden($role.' password (minimum 8 characters)', $validator);
        $confirmation = $io->askHidden('Confirm '.$role.' password', $validator);

        if (!\is_string($password) || !\is_string($confirmation)) {
            $io->error('Could not read the '.$role.' password.');

            return null;
        }

        if (!\hash_equals($password, $confirmation)) {
            $io->error($role.' passwords do not match.');

            return null;
        }

        return $password;
    }
}
