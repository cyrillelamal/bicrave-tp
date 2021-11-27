<?php

namespace App\Command;

use App\Entity\User;
use App\Security\Role;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(
    name: 'app:create-content-manager',
    description: 'Add a short description for your command',
)]
class CreateContentManagerCommand extends Command
{
    private UserPasswordHasherInterface $hasher;
    private EntityManagerInterface $entityManager;
    private TranslatorInterface $translator;
    private ValidatorInterface $validator;
    private LoggerInterface $logger;

    public function __construct(
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface      $entityManager,
        TranslatorInterface         $translator,
        ValidatorInterface          $validator,
        LoggerInterface             $logger,
    )
    {
        parent::__construct();
        $this->hasher = $hasher;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The user\'s email');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $email = $input->getArgument('email');

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $io->error($this->translator->trans('registration_form.email.bad_email', [], 'validators'));
            return Command::FAILURE;
        }

        $question = new Question($this->translator->trans('command.create_content_manager.password') . ': ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        do {
            $password = $helper->ask($input, $output, $question);
            if (mb_strlen($password) < 6) {
                $io->error($this->translator->trans('registration_form.password.min', ['{{ limit }}' => 6], 'validators'));
            }
        } while (mb_strlen($password) < 6);

        $user = new User();
        $user->addRole(Role::CONTENT_MANAGER);
        $user->setEmail($email);
        $user->setPassword($this->hasher->hashPassword($user, $password));

        $violations = $this->validator->validate($user);
        if ($violations->count()) {
            foreach ($violations as $violation) /** @var ConstraintViolationInterface $violation */ {
                $io->error($violation->getMessage());
            }
            return Command::FAILURE;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success($this->translator->trans('command.create_content_manager.success', ['%email%' => $email]));
        $this->logger->info("Created content manager: \"{$user->getEmail()}\"");

        return Command::SUCCESS;
    }
}
