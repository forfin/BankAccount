<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-17
 * Time: 17:49
 */

namespace App\Model\Account;


interface AccountCollection
{
    public function save(Account $user): void;

    public function get(string $accountNumber): ?Account;
}