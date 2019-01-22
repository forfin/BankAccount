<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-22
 * Time: 14:36
 */

namespace App\Controller;


use App\Projection\Account\AccountFinder;
use App\Projection\Account\TransactionFinder;
use Symfony\Component\HttpFoundation\JsonResponse;

class AccountController
{
    private $accountFinder;

    private $transactionFinder;

    public function __construct(AccountFinder $accountFinder, TransactionFinder $transactionFinder)
    {
        $this->accountFinder = $accountFinder;
        $this->transactionFinder = $transactionFinder;
    }

    public function show(string $accountNumber)
    {
        return JsonResponse::create($this->accountFinder->findByAccountNumber($accountNumber));
    }

    public function transactions(string $accountNumber)
    {
        return JsonResponse::create($this->transactionFinder->findByAccountNumber($accountNumber));
    }
}