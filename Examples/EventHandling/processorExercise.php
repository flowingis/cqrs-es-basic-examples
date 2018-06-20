<?php

require_once __DIR__. '/../../vendor/autoload.php';

class OrderPaymentRequested
{
//    private $orderId;
//    private $requestedAt;
//    /**
//     * @var
//     */
//    private $totalCost;

    //todo: add code to complete event implementation (is a php plain object)
}

class Pay
{
//    private $paymentId;
//    private $orderId;
//    private $totalCost;
//    /**
//     * @var \DateTimeImmutable
//     */
//    private $requestedAt;

    //todo: add code to complete command implementation (is a php plain object)
}

class PaymentDone
{
//    private $paymentId;
//
//    private $orderId;
//
//    /**
//     * @var \DateTimeImmutable
//     */
//    private $doneAt;

    //todo: add code to complete event implementation (is a php plain object)
}

class ConfirmOrder
{
//    private $orderId;
//    private $paymentId;
//    /**
//     * @var \DateTimeImmutable
//     */
//    private $confirmedAt;

    //todo: add code to complete command implementation (is a php plain object)
}

class Payment extends \Broadway\EventSourcing\EventSourcedAggregateRoot
{
    public static function do($paymentId, $orderId, \DateTimeImmutable $doneAt)
    {
        //todo: implement method
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
        //todo: implement method
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
        //todo: implement method
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
        //todo: implement method
    }

    public function handlePaymentDone(PaymentDone $event)
    {
        echo __METHOD__ . PHP_EOL;
        //todo: implement method
    }
}

//Todo: add code to publish OrderPaymentRequested event and run CheckoutProcess
// Run this cli command to verify result: docker-compose exec php php Examples/EventHandling/processorExercise.php
// Expected result is:
//CheckoutProcess::handleOrderPaymentRequested
//payment done
//CheckoutProcess::handlePaymentDone
//Order 1 confirmed%
