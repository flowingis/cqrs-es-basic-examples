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
        $this->userReadModelRepository->save(UserReadModel::register($event->getUserId()));
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

class UserReadModelRepository
{
    public function save(UserReadModel $user)
    {
        echo 'Read Model for user: ' . $user->getId() . ' saved';
    }
}

class UserReadModel
{
    private $id;

    private function __construct($id)
    {
        $this->id = $id;
    }

    public static function register($userId)
    {
        return new self($userId);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}

$eventBus = new \Broadway\EventHandling\SimpleEventBus();
$eventBus->subscribe(new UserProjector(new UserReadModelRepository()));

$domainMessage = \Broadway\Domain\DomainMessage::recordNow(
    '12345-dd',
    0,
    new \Broadway\Domain\Metadata(['source' => 'exmple of projection']),
    new UserRegistered(1, new \DateTimeImmutable())
    );
$domainEventStream = new \Broadway\Domain\DomainEventStream([$domainMessage]);

$eventBus->publish($domainEventStream);
