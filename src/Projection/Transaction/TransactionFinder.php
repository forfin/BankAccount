<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-22
 * Time: 14:42
 */

namespace App\Projection\Transaction;


use App\Projection\Table;
use Doctrine\DBAL\Connection;

class TransactionFinder
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function findByAccountNumber(string $accountNumber)
    {
        $stmt = $this->connection->prepare(\sprintf('SELECT * FROM %s WHERE account_number = :account_number', Table::TRANSACTION));
        $stmt->bindValue('account_number', $accountNumber);
        $stmt->execute();

        $result = $stmt->fetchAll();

        return $result;
    }
}