<?php

require_once __DIR__. '/../../vendor/autoload.php';

class RegisterUserCommand
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

    public function __construct(string $userID, string $firstName, string $lastName)
    {
        $this->userID = $userID;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getUserID(): string
    {
        return $this->userID;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }
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
        $user = User::register($command->getUserID(), $command->getFirstName(), $command->getLastName());

        $this->userRepository->save($user);
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

$registerUserCommand = new RegisterUserCommand('234-ae2', 'mario', 'rossi');
$commandBus = new \Broadway\CommandHandling\SimpleCommandBus();
$commandBus->subscribe(
    new UserRegistrationCommandHandler(
        new UserRepository()
    )
);

$commandBus->dispatch($registerUserCommand);
