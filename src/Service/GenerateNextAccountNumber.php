<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-17
 * Time: 11:32
 */

namespace App\Service;


use App\Projection\Account\AccountFinder;

class GenerateNextAccountNumber
{
    /**
     * @var AccountFinder
     */
    private $accountFinder;

    public function __construct(AccountFinder $accountFinder)
    {
        $this->accountFinder = $accountFinder;
    }

    public function __invoke()
    {
        return sprintf('%06d', count($this->accountFinder->findAll()) + 1);
    }
}