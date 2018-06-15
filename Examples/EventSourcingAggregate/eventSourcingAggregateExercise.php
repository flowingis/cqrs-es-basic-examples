<?php

require_once __DIR__. '/../../vendor/autoload.php';

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
        //Todo: create a coffee machine aggregate and save it with MachineAggregateRepository
    }

    public function handleSellCoffee(SellCoffee $sellCoffee)
    {
        // Todo: load coffee machine aggregate with MachineAggregateRepository, register a sold coffee and save the new
        // aggregate state.
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
        //Todo: implements machine create (apply CoffeeMachineCreated event)
    }

    public function registerSoldCoffee(string $machineId, int $quantity)
    {
        //Todo: implements coffee sold registration (apply CoffeeSold event)
    }

    protected function applyCoffeeMachineCreated(CoffeeMachineCreated $event)
    {
        //Todo: hydrate the aggregate state ($this->machineId)
    }

    protected function applyCoffeeSold(CoffeeSold $event)
    {
        //Todo: sum quantity sold ($this->quantity)
    }

    /**
     * @return string
     */
    public function getAggregateRootId(): string
    {
        return $this->machineId;
    }
}

$eventBus = new \Broadway\EventHandling\SimpleEventBus();
$machineAggregateRepository = new MachineAggregateRepository(
    new \Broadway\EventStore\InMemoryEventStore(),
    $eventBus,
    new Broadway\EventSourcing\AggregateFactory\PublicConstructorAggregateFactory()
);

//Todo: create MachineCommandHandler instance


$commandBus = new \Broadway\CommandHandling\SimpleCommandBus();
$commandBus->subscribe($machineCommandHandler);

//Todo: dispatch CreateCoffeeMachine command with command bus

//Todo: load coffee machine aggregate by id, with MachineAggregateRepository (remove comment below)
//$coffeeMachine = ...

//Todo: remove comment below
// echo 'Aggregate with id: ' . $coffeeMachine->getAggregateRootId() . ' created.' . PHP_EOL;


//Todo: dispatch SellCoffee command with command bus

//Todo: load coffee machine aggregate by id, with MachineAggregateRepository (remove comment below)
//$coffeeMachine = ...

//Todo: remove comments below
//echo 'Coffee Sold. Aggregate dump: ' . PHP_EOL;
//var_dump($coffeeMachine);


// Run this cli command to verify result: docker-compose exec php php Examples/EventSourcingAggregate/eventSourcingAggregateExercise.php
// Expected result is:
//Aggregate with id: ma-0001 created.
//Coffee Sold. Aggregate dump:
//object(CoffeeMachine)#20 (4) {
//["machineId":"CoffeeMachine":private]=>
//  string(7) "ma-0001"
//["quantity":"CoffeeMachine":private]=>
//  int(20)
//  ["uncommittedEvents":"Broadway\EventSourcing\EventSourcedAggregateRoot":private]=>
//  array(0) {
//}
//  ["playhead":"Broadway\EventSourcing\EventSourcedAggregateRoot":private]=>
//  int(2)
//}
