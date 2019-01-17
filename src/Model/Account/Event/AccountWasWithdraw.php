<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-18
 * Time: 05:17
 */

namespace App\Model\Account\Event;


use Prooph\EventSourcing\AggregateChanged;

class AccountWasWithdraw extends AggregateChanged
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
        return $this->accountNumber;
    }

    public function amount()
    {
        return $this->amount;
    }
}