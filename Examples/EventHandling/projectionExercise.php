<?php

require_once __DIR__. '/../../vendor/autoload.php';

class UserProjector extends \Broadway\ReadModel\Projector
{
    /**
     * @var UserReadModelRepository
     */
    private $userReadModelRepository;

    public function __construct(UserReadModelRepository $userReadModelRepository)
    {
        $this->userReadModelRepository = $userReadModelRepository;
    }

    public function applyUserRegistered(UserRegistered $event)
    {
        // Toto: add code to complete projection to save a UserReadModel using UserReadModelRepository
    }
}

class UserRegistered
{
//    private $userId;
//    /**
//     * @var DateTime
//     */
//    private $occurredAt;

// Todo: add code to complete event implementation (is a php plain object)

}

class UserReadModelRepository
{
    public function save(UserReadModel $user)
    {
        echo 'Read Model for user: ' . $user->getId() . ' saved';
    }
}

class UserReadModel
{
//    private $id;

// Todo: add code to complete Read Model (is a php plain object)

}

//Todo: add code to publish UserRegistered event and run UserProjector to save UserReadModel
// Run this cli command to verify result: docker-compose exec php php Examples/CommandHandling/projectionExercise.php
// Expected result is: Read Model for user: 1 saved
