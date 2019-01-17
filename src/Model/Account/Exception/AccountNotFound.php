<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-18
 * Time: 05:35
 */

namespace App\Model\Account\Exception;


class AccountNotFound extends \InvalidArgumentException
{
    public static function withAccountNumber(string $accountNumber)
    {
        return new self(\sprintf('Account number %s cannot be found.', $accountNumber));
    }
}