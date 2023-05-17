<?php

// src/Command/CreateUserCommand.php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user.',
    hidden: false,
    aliases: ['app:add-user']
)]
class CreateUserCommand extends Command
{
    protected static $defaultDescription = 'Creates a new user.';

    private $entityManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);

        $helper = $this->getHelper('question');

        // ask for email
        $emailQuestion = new Question('Enter the email address: ');
        $email = $helper->ask($input, $output, $emailQuestion);

        // validate email format
        $validator = Validation::createValidator();
        $violations = $validator->validate($email, [
            new Email([
                'mode' => 'strict',
                'message' => 'The email address "{{ value }}" is not a valid email address.',
            ]),
        ]);
        if (count($violations) > 0) {
            $output->writeln((string) $violations);
            return Command::FAILURE;
        }

        // ask for password
        $passwordQuestion = new Question('Enter the password: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $passwordQuestion);

        // ask for password confirmation
        $confirmQuestion = new Question('Confirm the password: ');
        $confirmQuestion->setHidden(true);
        $confirmQuestion->setHiddenFallback(false);
        $confirm = $helper->ask($input, $output, $confirmQuestion);

        // check if passwords match
        if ($password !== $confirm) {
            $output->writeln('The passwords do not match.');
            return Command::FAILURE;
        }

        // Create a new user
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->hashPassword($user, $password));
        $user->setRoles(['ROLE_ADMIN']);

        // Persist the user in the database
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('User created successfully.');

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to create a user...')
        ;
    }
}
