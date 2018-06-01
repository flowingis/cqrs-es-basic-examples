<?php

require_once __DIR__. '/../../vendor/autoload.php';

class UserRegistrationListener implements Broadway\EventHandling\EventListener
{
    /**
     * @param \Broadway\Domain\DomainMessage $domainMessage
     */
    public function handle(\Broadway\Domain\DomainMessage $domainMessage)
    {
        // Toto: add code echo e message with userId
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

//Todo: add code to publish UserRegistered event and echo a message from event listener
// Run this cli command to verify result: docker-compose exec php php Examples/CommandHandling/eventHandlingExercise.php
// Expected result is: user successfully registered: 1
