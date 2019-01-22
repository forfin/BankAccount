<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-18
 * Time: 05:17
 */

namespace App\Model\Account\Event;


use Prooph\EventSourcing\AggregateChanged;

class AccountWasDeposit extends AggregateChanged
{
    /**
     * @var string
     */
    private $accountNumber;

    /**
     * @var float
     */
    private $amount;

    public static function withData(string $accountNumber, float $amount)
    {
        /** @var self $event */
        $event = self::occur($accountNumber, [
            'amount' => $amount,
        ]);

        $event->accountNumber = $accountNumber;
        $event->amount = $amount;

        return $event;
    }

    public function accountNumber()
    {
        if (null === $this->accountNumber) {
            $this->accountNumber = $this->aggregateId();
        }

        return $this->accountNumber;
    }

    public function amount()
    {
        if (null === $this->amount) {
            $this->amount = $this->payload['amount'];
        }

        return $this->amount;
    }
}