<?php

namespace GeekBrains\LevelTwo\Blog\Commands;

use GeekBrains\LevelTwo\Blog\Exceptions\ArgumentsException;
use GeekBrains\LevelTwo\Blog\Exceptions\CommandException;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Person\Name;
use Psr\Log\LoggerInterface;

//php cli.php username=ivan first_name=Ivan last_name=Nikitin

class CreateUserCommand
{

// Команда зависит от контракта репозитория пользователей,
// а не от конкретной реализации
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger,
    )
    {
    }

    /**
     * @throws CommandException
     * @throws InvalidArgumentException|ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');

// Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
            $this->logger->warning("User already exists: $username");
// Бросаем исключение, если пользователь уже существует
            throw new CommandException("User already exists: $username");
        }

        $uuid = UUID::random();
        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save(new User(
            $uuid,
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name')),
            $username,
        ));
        $this->logger->info("User created: $uuid");
    }

    private function userExists(string $username): bool
    {
        try {
        // Пытаемся получить пользователя из репозитория
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }



}