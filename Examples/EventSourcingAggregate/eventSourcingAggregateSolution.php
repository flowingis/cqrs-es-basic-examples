<?php

require_once __DIR__. '/../../vendor/autoload.php';

class CreateCoffeeMachine
{
    /**
     * @var string
     */
    private $machineId;

    public function __construct(string $machineId)
    {
        $this->machineId = $machineId;
    }

    /**
     * @return string
     */
    public function getMachineId(): string
    {
        return $this->machineId;
    }
}

class MachineCommandHandler extends \Broadway\CommandHandling\SimpleCommandHandler
{
    /**
     * @var \Broadway\Repository\Repository
     */
    private $repository;

    public function __construct(\Broadway\Repository\Repository $repository)
    {
        $this->repository = $repository;
    }

    public function handleCreateCoffeeMachine(CreateCoffeeMachine $createCoffeeMachine)
    {
        $coffeeMachine = CoffeeMachine::create($createCoffeeMachine->getMachineId());

        $this->repository->save($coffeeMachine);
    }

    public function handleSellCoffee(SellCoffee $sellCoffee)
    {
        /** @var CoffeeMachine $coffeeMachine */
        $coffeeMachine = $this->repository->load($sellCoffee->getMachineId());
        $coffeeMachine->registerSoldCoffee($sellCoffee->getMachineId(), $sellCoffee->getQuantity());
        $this->repository->save($coffeeMachine);
    }
}

class CoffeeMachineCreated
{
    /**
     * @var string
     */
    private $machineId;

    public function __construct(string $machineId)
    {
        $this->machineId = $machineId;
    }

    /**
     * @return string
     */
    public function getMachineId(): string
    {
        return $this->machineId;
    }
}

class CoffeeMachine extends \Broadway\EventSourcing\EventSourcedAggregateRoot
{
    /**
     * @var string
     */
    private $machineId;
    /**
     * @var int
     */
    private $quantity = 0;

    public static function create(string $machineId): CoffeeMachine
    {
        $coffeeMachine = new self();
        $coffeeMachine->apply(new CoffeeMachineCreated($machineId));

        return $coffeeMachine;
    }

    public function registerSoldCoffee(string $machineId, int $quantity)
    {
        $this->apply(
            new CoffeeSold($machineId, $quantity)
        );
    }

    protected function applyCoffeeMachineCreated(CoffeeMachineCreated $event)
    {
        $this->machineId = $event->getMachineId();
    }

    protected function applyCoffeeSold(CoffeeSold $event)
    {
        $this->quantity += $event->getQuantity();
    }

    /**
     * @return string
     */
    public function getAggregateRootId(): string
    {
        return $this->machineId;
    }
}

class SellCoffee
{
    /**
     * @var string
     */
    private $machineId;
    /**
     * @var int
     */
    private $quantity;

    public function __construct(string $machineId, int $quantity)
    {
        $this->machineId = $machineId;
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getMachineId(): string
    {
        return $this->machineId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }
}

class CoffeeSold
{
    /**
     * @var string
     */
    private $machineId;
    /**
     * @var int
     */
    private $quantity;

    public function __construct(string $machineId, int $quantity)
    {
        $this->machineId = $machineId;
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }
}

class MachineAggregateRepository extends \Broadway\EventSourcing\EventSourcingRepository
{
    public function __construct(
        \Broadway\EventStore\EventStore $eventStore,
        \Broadway\EventHandling\EventBus $eventBus,
        \Broadway\EventSourcing\AggregateFactory\AggregateFactory $aggregateFactory,
        $eventStreamDecorators = []
    ) {
        parent::__construct(
            $eventStore,
            $eventBus,
            '\CoffeeMachine',
            $aggregateFactory,
            $eventStreamDecorators
        );
    }
}

$eventBus = new \Broadway\EventHandling\SimpleEventBus();
$machineAggregateRepository = new MachineAggregateRepository(
    new \Broadway\EventStore\InMemoryEventStore(),
    $eventBus,
    new Broadway\EventSourcing\AggregateFactory\PublicConstructorAggregateFactory()
);
$machineCommandHandler = new MachineCommandHandler($machineAggregateRepository);

$commandBus = new \Broadway\CommandHandling\SimpleCommandBus();
$commandBus->subscribe($machineCommandHandler);

$commandBus->dispatch(new CreateCoffeeMachine('ma-0001'));

$coffeeMachine = $machineAggregateRepository->load('ma-0001');

echo 'Aggregate with id: ' . $coffeeMachine->getAggregateRootId() . ' created.' . PHP_EOL;

$commandBus->dispatch(new SellCoffee('ma-0001', 10));
$commandBus->dispatch(new SellCoffee('ma-0001', 10));

$coffeeMachine = $machineAggregateRepository->load('ma-0001');

echo 'Coffee Sold. Aggregate dump: ' . PHP_EOL;
var_dump($coffeeMachine);
