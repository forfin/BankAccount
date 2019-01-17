<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-17
 * Time: 11:14
 */

namespace App\Model\Handler\Account;


use App\Model\Account\Account;
use App\Model\Account\AccountCollection;
use App\Model\Command\RegisterAccount;
use App\Service\GenerateNextAccountNumber;

class RegisterAccountHandler
{

    /**
     * @var AccountCollection
     */
    private $accountCollection;

    /**
     * @var GenerateNextAccountNumber
     */
    private $generateNextAccountNumber;

    public function __construct(AccountCollection $accountCollection, GenerateNextAccountNumber $generateNextAccountNumber)
    {
        $this->generateNextAccountNumber = $generateNextAccountNumber;
        $this->accountCollection = $accountCollection;
    }

    public function __invoke(RegisterAccount $registerCommand)
    {
        $accountNumber = ($this->generateNextAccountNumber)();

        $account = Account::registerWithData($accountNumber, $registerCommand->name());
        $this->accountCollection->save($account);
    }

}