<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-18
 * Time: 04:59
 */

namespace App\Model\Command;


use Assert\Assertion;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class DepositAccount extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public function accountNumber(): string
    {
        return $this->payload['account_number'];
    }

    public function amount(): float
    {
        return $this->payload['amount'];
    }

    protected function setPayload(array $payload): void
    {
        Assertion::keyExists($payload, 'account_number');
        Assertion::string($payload['account_number']);
        Assertion::keyExists($payload, 'amount');
        Assertion::float($payload, 'amount');
        Assertion::greaterThan($payload, 0);

        $this->payload = $payload;
    }

}