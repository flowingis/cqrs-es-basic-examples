<?php

require_once __DIR__. '/../../vendor/autoload.php';

class RegisterUserCommand
{
//    /**
//     * @var string
//     */
//    private $userID;
//    /**
//     * @var string
//     */
//    private $firstName;
//    /**
//     * @var string
//     */
//    private $lastName;

    // Todo: add code to complete command implementation (is a php plain object)
}

class UserRegistrationCommandHandler extends \Broadway\CommandHandling\SimpleCommandHandler
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handleRegisterUserCommand(RegisterUserCommand $command): void
    {
        // Toto: add code complete command handler to save a User using UserRepository
    }
}

class User
{
    /**
     * @var string
     */
    private $userID;
    /**
     * @var string
     */
    private $firstName;
    /**
     * @var string
     */
    private $lastName;

    private function __construct(string $userID, string $firstName, string $lastName)
    {
        $this->userID = $userID;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function userInfo()
    {
        return sprintf(
            'ID : %s, First Name: %s, Last Name: %s',
            $this->userID,
            $this->firstName,
            $this->lastName
        );
    }

    public static function register(string $userID, string $firstName, string $lastName): User
    {
        return new self($userID, $firstName, $lastName);
    }
}

class UserRepository
{
    public function save(User $user): void
    {
        echo $user->userInfo();
    }
}

//Todo: add code to dispatch RegisterUserCommand command and save User
// Run this cli command to verify result: docker-compose exec php php Examples/CommandHandling/commandHandlingExercise.php
// Expected result is: ID : YOUR_ID, First Name: mario, Last Name: rossi
