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
use App\Model\Account\Exception\NotEnoughBalance;
use App\Model\Command\WithdrawAccount;

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

    public function __invoke(WithdrawAccount $command): void
    {
        $account = $this->accountCollection->get($command->accountNumber());

        if (! $account) {
            throw AccountNotFound::withAccountNumber($command->accountNumber());
        }

        if ($account->balance() < $command->amount()) {
            throw NotEnoughBalance::withAccountNumber($command->accountNumber());
        }

        $account->withdraw($command->amount());

        $this->accountCollection->save($account);
    }
}