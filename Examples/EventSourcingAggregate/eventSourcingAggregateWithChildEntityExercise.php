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

class SetAlarm
{
    /**
     * @var string
     */
    private $machineId;
    /**
     * @var int
     */
    private $supplyQuantity;
    /**
     * @var string
     */
    private $type;

    public function __construct(string $machineId, string $type, int $supplyQuantity)
    {
        $this->machineId = $machineId;
        $this->supplyQuantity = $supplyQuantity;
        $this->type = $type;
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
    public function getSupplyQuantity(): int
    {
        return $this->supplyQuantity;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}

class AlarmWasSetting
{
    /**
     * @var string
     */
    private $machineId;
    /**
     * @var int
     */
    private $supplyQuantity;
    /**
     * @var string
     */
    private $alarmType;

    public function __construct(string $machineId, string $alarmType, int $supplyQuantity)
    {
        $this->machineId = $machineId;
        $this->supplyQuantity = $supplyQuantity;
        $this->alarmType = $alarmType;
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
    public function getSupplyQuantity(): int
    {
        return $this->supplyQuantity;
    }

    /**
     * @return string
     */
    public function getAlarmType(): string
    {
        return $this->alarmType;
    }
}

class AlarmNotified
{
    /**
     * @var string
     */
    private $machineId;
    /**
     * @var string
     */
    private $alarmType;

    public function __construct(string $machineId, string $alarmType)
    {
        $this->machineId = $machineId;
        $this->alarmType = $alarmType;
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

    public function handleSetAlarm(SetAlarm $setAlarm)
    {
        // Todo: load coffee machine aggregate with MachineAggregateRepository, set alarm and save the new
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
    /**
     * @var Alarm
     */
    private $alarm;

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

        if($this->alarm){
            $this->alarm->checkSupply($this->quantity);
        }
    }

    public function setAlarm(string $machineId, string $alarmType, int $supplyQuantity)
    {
        //Todo: implements alarm setting using Alarm entity child (apply AlarmWasSetting event)
    }

    protected function applyCoffeeMachineCreated(CoffeeMachineCreated $event)
    {
        $this->machineId = $event->getMachineId();
    }

    protected function applyCoffeeSold(CoffeeSold $event)
    {
        $this->quantity += $event->getQuantity();
    }

    protected function applyAlarmWasSetting(AlarmWasSetting $event)
    {
        //Todo: create Alarm entity child
    }

    /**
     * @return string
     */
    public function getAggregateRootId(): string
    {
        return $this->machineId;
    }

    protected function getChildEntities(): array
    {
        if (!$this->alarm) {
            return [];
        }

        return [$this->alarm];
    }
}

class Alarm extends \Broadway\EventSourcing\SimpleEventSourcedEntity
{
    /**
     * @var string
     */
    private $machineId;
    /**
     * @var int
     */
    private $supplyQuantity;
    /**
     * @var bool
     */
    private $alarmNotified = false;
    /**
     * @var string
     */
    private $type;

    public function __construct(string $machineId, string $type, int $supplyQuantity)
    {
        $this->machineId = $machineId;
        $this->supplyQuantity = $supplyQuantity;
        $this->type = $type;
    }

    public function checkSupply(int $consumedQuantity)
    {
        // Todo: check if supplyQuantity is <= of $consumedQuantity and apply AlarmNotified
    }

    protected function applyAlarmNotified(AlarmNotified $event)
    {
        // Todo: set alarmNotified to true
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

$coffeeMachine = $machineAggregateRepository->load('ma-0001');

echo 'Coffee Sold. Aggregate dump: ' . PHP_EOL;
var_dump($coffeeMachine);


//Todo: dispatch SetAlarm command with command bus

//Todo: dispatch SellCoffee command with command bus to complete the coffee supply

//Todo: load coffee machine aggregate by id, with MachineAggregateRepository (remove comment below)
//$coffeeMachine = ...

//Todo: remove comments below
//echo 'Alarm sent: supply finished' . PHP_EOL;
//var_dump($coffeeMachine);

// Run this cli command to verify result: docker-compose exec php php Examples/EventSourcingAggregate/eventSourcingAggregateExercise.php
// Expected result is:
//Aggregate with id: ma-0001 created.
//Coffee Sold. Aggregate dump:
//object(CoffeeMachine)#14 (5) {
//["machineId":"CoffeeMachine":private]=>
//  string(7) "ma-0001"
//["quantity":"CoffeeMachine":private]=>
//  int(10)
//  ["alarm":"CoffeeMachine":private]=>
//  NULL
//  ["uncommittedEvents":"Broadway\EventSourcing\EventSourcedAggregateRoot":private]=>
//  array(0) {
//}
//  ["playhead":"Broadway\EventSourcing\EventSourcedAggregateRoot":private]=>
//  int(1)
//}
//Alarm sent: supply finished
//object(CoffeeMachine)#38 (5) {
//["machineId":"CoffeeMachine":private]=>
//  string(7) "ma-0001"
//["quantity":"CoffeeMachine":private]=>
//  int(20)
//  ["alarm":"CoffeeMachine":private]=>
//  object(Alarm)#42 (5) {
//  ["machineId":"Alarm":private]=>
//    string(7) "ma-0001"
//["supplyQuantity":"Alarm":private]=>
//    int(20)
//    ["alarmNotified":"Alarm":private]=>
//    bool(true)
//    ["type":"Alarm":private]=>
//    string(15) "supply finished"
//["aggregateRoot":"Broadway\EventSourcing\SimpleEventSourcedEntity":private]=>
//    *RECURSION*
//  }
//  ["uncommittedEvents":"Broadway\EventSourcing\EventSourcedAggregateRoot":private]=>
//  array(0) {
//}
//  ["playhead":"Broadway\EventSourcing\EventSourcedAggregateRoot":private]=>
//  int(4)
//}
