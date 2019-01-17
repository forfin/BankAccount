<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-17
 * Time: 17:59
 */

namespace App\Service;


use App\Model\Account\Account;
use App\Model\Account\AccountCollection;
use Prooph\EventSourcing\Aggregate\AggregateRepository;

class EventStoreAccountCollection extends AggregateRepository implements AccountCollection
{
    public function save(Account $account): void
    {
        $this->saveAggregateRoot($account);
    }

    public function get(string $accountNumber): ?Account
    {
        return $this->getAggregateRoot($accountNumber);
    }

}