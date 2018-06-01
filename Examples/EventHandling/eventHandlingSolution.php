<?php

require_once __DIR__. '/../../vendor/autoload.php';

class UserRegistrationListener implements Broadway\EventHandling\EventListener
{
    /**
     * @param \Broadway\Domain\DomainMessage $domainMessage
     */
    public function handle(\Broadway\Domain\DomainMessage $domainMessage)
    {
        echo "user successfully registered: " . $domainMessage->getPayload()->getUserId();
    }
}

class UserRegistered
{
    private $userId;
    /**
     * @var DateTime
     */
    private $occurredAt;

    public function __construct($userId, \DateTimeImmutable $occurredAt)
    {
        $this->userId = $userId;
        $this->occurredAt = $occurredAt;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }
}

$eventBus = new \Broadway\EventHandling\SimpleEventBus();
$eventBus->subscribe(new UserRegistrationListener());

$domainMessage = \Broadway\Domain\DomainMessage::recordNow(
    '12345-dd',
    0,
    new \Broadway\Domain\Metadata(['source' => 'example']),
    new UserRegistered(1, new \DateTimeImmutable())
);

$domainEventStream = new \Broadway\Domain\DomainEventStream([$domainMessage]);

$eventBus->publish($domainEventStream);
