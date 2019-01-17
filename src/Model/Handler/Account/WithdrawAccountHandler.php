<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-18
 * Time: 05:04
 */

namespace App\Model\Handler\Account;


use App\Model\Account\AccountCollection;
use App\Model\Account\Event\AccountWasDeposit;
use App\Model\Account\Exception\AccountNotFound;

class WithdrawAccountHandler
{
    /**
     * @var AccountCollection
     */
    private $accountCollection;

    public function __construct(AccountCollection $accountCollection)
    {
        $this->accountCollection = $accountCollection;
    }

    public function __invoke(AccountWasDeposit $command): void
    {
        $account = $this->accountCollection->get($command->accountNumber());

        if (! $account) {
            throw AccountNotFound::withAccountNumber($command->accountNumber());
        }

        $account->withdraw($command->amount());

        $this->accountCollection->save($account);
    }
}