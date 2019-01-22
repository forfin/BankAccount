<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-17
 * Time: 21:22
 */

namespace App\Projection\Account;


use App\Projection\Table;
use Doctrine\DBAL\Connection;

class AccountFinder
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

    public function findAll(): array
    {
        return $this->connection->fetchAll(\sprintf('SELECT * FROM %s', Table::ACCOUNT));
    }

    public function findByAccountNumber(string $accountNumber): ?\stdClass
    {
        $stmt = $this->connection->prepare(\sprintf('SELECT * FROM %s WHERE account_number = :account_number', Table::ACCOUNT));
        $stmt->bindValue('account_number', $accountNumber);
        $stmt->execute();

        $result = $stmt->fetch();

        if (false === $result) {
            return null;
        }

        return $result;
    }
}