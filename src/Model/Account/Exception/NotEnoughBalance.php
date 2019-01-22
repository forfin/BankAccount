<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-22
 * Time: 12:57
 */

namespace App\Model\Account\Exception;


class NotEnoughBalance extends \InvalidArgumentException
{
    public static function withAccountNumber(string $accountNumber)
    {
        return new self(\sprintf('Account number %s does not have enough balance.', $accountNumber));
    }
}