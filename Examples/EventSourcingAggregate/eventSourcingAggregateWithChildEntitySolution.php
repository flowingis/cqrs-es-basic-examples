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
        /** @var CoffeeMachine $coffeeMachine */
        $coffeeMachine = $this->repository->load($setAlarm->getMachineId());
        $coffeeMachine->setAlarm($setAlarm->getMachineId(), $setAlarm->getType(), $setAlarm->getSupplyQuantity());
        $this->repository->save($coffeeMachine);
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
        $this->apply(
            new AlarmWasSetting($machineId, $alarmType, $supplyQuantity)
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

    protected function applyAlarmWasSetting(AlarmWasSetting $event)
    {
        $this->alarm = new Alarm($event->getMachineId(), $event->getAlarmType(), $event->getSupplyQuantity());
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
        if ($this->supplyQuantity <= $consumedQuantity) {
            $this->apply(new AlarmNotified($this->machineId, $this->type));
        }
    }

    protected function applyAlarmNotified(AlarmNotified $event)
    {
        $this->alarmNotified = true;
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

$commandBus->dispatch(new SetAlarm('ma-0001', 'supply finished', 20));
$commandBus->dispatch(new SellCoffee('ma-0001', 10));

$coffeeMachine = $machineAggregateRepository->load('ma-0001');

echo 'Alarm sent: supply finished' . PHP_EOL;
var_dump($coffeeMachine);
