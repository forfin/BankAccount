<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-18
 * Time: 05:04
 */

namespace App\Model\Handler\Account;


use App\Model\Account\AccountCollection;
use App\Model\Account\Exception\AccountNotFound;
use App\Model\Command\DepositAccount;

class DepositAccountHandler
{
    /**
     * @var AccountCollection
     */
    private $accountCollection;

    public function __construct(AccountCollection $accountCollection)
    {
        $this->accountCollection = $accountCollection;
    }

    public function __invoke(DepositAccount $command): void
    {
        $account = $this->accountCollection->get($command->accountNumber());

        if (! $account) {
            throw AccountNotFound::withAccountNumber($command->accountNumber());
        }

        $account->deposit($command->amount());

        $this->accountCollection->save($account);
    }
}