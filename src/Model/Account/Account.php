<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-17
 * Time: 10:11
 */

namespace App\Model\Account;

use App\Model\Account\Event\AccountWasDeposit;
use App\Model\Account\Event\AccountWasRegistered;
use App\Model\Account\Event\AccountWasWithdraw;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;

/**
 * Class Account
 * @package App\Model\Account
 */
class Account extends AggregateRoot
{

    /**
     * @var string
     */
    private $accountNumber;

    public static function registerWithData(
        string $accountNumber,
        string $name
    ): self {
        $self = new self();

        $self->recordThat(AccountWasRegistered::withData($accountNumber, $name));
        $self->accountNumber = $accountNumber;

        return $self;
    }

    public function deposit(float $amount)
    {
        $this->recordThat(AccountWasDeposit::withData($this->accountNumber, $amount));
    }

    public function withdraw(float $amount)
    {
        $this->recordThat(AccountWasWithdraw::withData($this->accountNumber, $amount));
    }

    protected function aggregateId(): string
    {
        return $this->accountNumber;
    }

    protected function apply(AggregateChanged $event): void
    {
        $handler = $this->determineEventHandlerMethodFor($event);

        if (! \method_exists($this, $handler)) {
            throw new \RuntimeException(\sprintf(
                'Missing event handler method %s for aggregate root %s',
                $handler,
                \get_class($this)
            ));
        }

        $this->{$handler}($event);
    }

    protected function determineEventHandlerMethodFor(AggregateChanged $e): string
    {
        return 'when' . \implode(\array_slice(\explode('\\', \get_class($e)), -1));
    }

    protected function whenAccountWasRegistered(AccountWasRegistered $event)
    {
        $this->accountNumber = $event->accountNumber();
    }

    protected function whenAccountWasDeposit(AccountWasDeposit $event)
    {

    }
}