<?php

require_once __DIR__. '/../../vendor/autoload.php';

class OrderPaymentRequested
{
    private $orderId;
    private $requestedAt;
    /**
     * @var
     */
    private $totalCost;

    public function __construct($orderId, $totalCost, \DateTimeImmutable $requestedAt)
    {
        $this->orderId = $orderId;
        $this->requestedAt = $requestedAt;
        $this->totalCost = $totalCost;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
    }

    /**
     * @return mixed
     */
    public function getTotalCost()
    {
        return $this->totalCost;
    }
}

class Pay
{
    private $paymentId;
    private $orderId;
    private $totalCost;
    /**
     * @var \DateTimeImmutable
     */
    private $requestedAt;

    public function __construct($paymentId, $orderId, $totalCost, \DateTimeImmutable $requestedAt)
    {
        $this->paymentId = $paymentId;
        $this->orderId = $orderId;
        $this->totalCost = $totalCost;
        $this->requestedAt = $requestedAt;
    }

    public function getPaymentId()
    {
        return $this->paymentId;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return mixed
     */
    public function getTotalCost()
    {
        return $this->totalCost;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
    }
}

class PaymentDone
{
    private $paymentId;

    private $orderId;

    /**
     * @var \DateTimeImmutable
     */
    private $doneAt;

    public function __construct($paymentId, $orderId, \DateTimeImmutable $doneAt)
    {
        $this->paymentId = $paymentId;
        $this->orderId = $orderId;
        $this->doneAt = $doneAt;
    }

    public function getPaymentId()
    {
        return $this->paymentId;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDoneAt()
    {
        return $this->doneAt;
    }
}

class ConfirmOrder
{
    private $orderId;
    private $paymentId;
    /**
     * @var \DateTimeImmutable
     */
    private $confirmedAt;

    public function __construct($orderId, $paymentId, \DateTimeImmutable $confirmedAt)
    {
        $this->orderId = $orderId;
        $this->paymentId = $paymentId;
        $this->confirmedAt = $confirmedAt;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getConfirmedAt(): \DateTimeImmutable
    {
        return $this->confirmedAt;
    }
}

class Payment extends \Broadway\EventSourcing\EventSourcedAggregateRoot
{
    public static function do($paymentId, $orderId, \DateTimeImmutable $doneAt)
    {
        $payment = new self();
        $payment->apply(new PaymentDone($paymentId, $orderId, $doneAt));

        return $payment;
    }
    /**
     * @return string
     */
    public function getAggregateRootId(): string
    {
        return '';
    }
}

class PaymentCommandHandler extends \Broadway\CommandHandling\SimpleCommandHandler
{
    private $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function handlePay(Pay $command): void
    {
        $payment = Payment::do($command->getPaymentId(), $command->getOrderId(), $command->getRequestedAt());

        $this->paymentRepository->save($payment);
    }
}

class PaymentRepository
{
    /**
     * @var \Broadway\EventHandling\EventBus
     */
    private $eventBus;

    public function __construct(\Broadway\EventHandling\EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function save(Payment $payment): void
    {
        $this->eventBus->publish($payment->getUncommittedEvents());
        echo 'payment done' . PHP_EOL;
    }
}

class OrderCommandHandler extends \Broadway\CommandHandling\SimpleCommandHandler
{
    public function handleConfirmOrder(ConfirmOrder $command): void
    {
        echo 'Order ' . $command->getOrderId() . ' confirmed';
    }
}

class CheckoutProcess extends \Broadway\Processor\Processor
{
    /**
     * @var \Broadway\CommandHandling\CommandBus
     */
    private $commandBus;

    public function __construct(\Broadway\CommandHandling\CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function handleOrderPaymentRequested(OrderPaymentRequested $event)
    {
        echo __METHOD__ . PHP_EOL;
        $this->commandBus->dispatch(new Pay(
            time(),
            $event->getOrderId(),
            $event->getTotalCost(),
            $event->getRequestedAt()
        ));
    }

    public function handlePaymentDone(PaymentDone $event)
    {
        echo __METHOD__ . PHP_EOL;
        $this->commandBus->dispatch(new ConfirmOrder(
            $event->getOrderId(),
            $event->getPaymentId(),
            $event->getDoneAt()
        ));
    }
}

$eventBus = new \Broadway\EventHandling\SimpleEventBus();
$commandBus = new \Broadway\CommandHandling\SimpleCommandBus();
$commandBus->subscribe(new PaymentCommandHandler(new PaymentRepository($eventBus)));
$commandBus->subscribe(new OrderCommandHandler());
$eventBus->subscribe(new CheckoutProcess($commandBus));

$domainMessage = \Broadway\Domain\DomainMessage::recordNow(
    '12345-dd',
    0,
    new \Broadway\Domain\Metadata(['source' => 'exmple of projection']),
    new OrderPaymentRequested(1, 10, new \DateTimeImmutable())
    );
$domainEventStream = new \Broadway\Domain\DomainEventStream([$domainMessage]);

$eventBus->publish($domainEventStream);
