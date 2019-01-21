<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-17
 * Time: 11:02
 */

namespace App\Model\Account\Event;

use Prooph\EventSourcing\AggregateChanged;

class AccountWasRegistered extends AggregateChanged
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $accountNumber;

    public function name(): string
    {
        if (null === $this->name) {
            $this->name = $this->payload['name'];
        }

        return $this->name;
    }

    public function accountNumber(): string
    {
        if (null === $this->accountNumber) {
            $this->accountNumber = $this->aggregateId();
        }

        return $this->accountNumber;
    }

    public static function withData(string $accountNumber, string $name): self
    {
        /** @var self $event */
        $event = self::occur($accountNumber, [
            'name' => $name,
        ]);

        $event->accountNumber = $accountNumber;
        $event->name = $name;

        return $event;
    }
}