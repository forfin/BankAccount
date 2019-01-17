<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-17
 * Time: 11:19
 */

namespace App\Model\Command;


use Assert\Assertion;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class RegisterAccount extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public function name(): string
    {
        return $this->payload['name'];
    }

    protected function setPayload(array $payload): void
    {
        Assertion::keyExists($payload, 'name');
        Assertion::string($payload['name']);

        $this->payload = $payload;
    }

}