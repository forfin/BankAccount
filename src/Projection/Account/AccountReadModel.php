<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-17
 * Time: 12:52
 */

namespace App\Projection\Account;


use App\Projection\Table;
use Doctrine\DBAL\Connection;
use Prooph\EventStore\Projection\AbstractReadModel;

class AccountReadModel extends AbstractReadModel
{

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function init(): void
    {
        $tableName = Table::ACCOUNT;

        $sql = <<<EOT
CREATE TABLE `$tableName` (
  `account_number` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `total_amount` real COLLATE utf8_unicode_ci NOT NULL,
  `transaction` JSON utf8_unicode_ci,
  PRIMARY KEY (`account_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOT;

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    public function isInitialized(): bool
    {
        $tableName = Table::ACCOUNT;

        $sql = "SHOW TABLES LIKE '$tableName';";

        $statement = $this->connection->prepare($sql);
        $statement->execute();

        $result = $statement->fetch();

        if (false === $result) {
            return false;
        }

        return true;
    }

    public function reset(): void
    {
        $tableName = Table::ACCOUNT;

        $sql = "TRUNCATE TABLE $tableName;";

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    public function delete(): void
    {
        $tableName = Table::ACCOUNT;

        $sql = "DROP TABLE $tableName;";

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    protected function insert(array $data): void
    {
        $this->connection->insert(Table::ACCOUNT, $data);
    }

    protected function deposit(string $accountNumber, float $amount): void
    {
        $transactionJson = json_encode([
            'type' => 'deposit',
            'amount' => $amount
        ]);

        $this->updateTable($accountNumber, $amount, $transactionJson);
    }

    protected function withdraw(string $accountNumber, float $amount): void
    {
        $transactionJson = json_encode([
            'type' => 'withdraw',
            'amount' => $amount
        ]);

        $this->updateTable($accountNumber, $amount, $transactionJson);
    }

    private function updateTable(string $accountNumber, float $amount, string $transactionJson): void
    {
        $tableName = Table::ACCOUNT;
        $sql = <<<SQL
        UPDATE ``$tableName`` SET 
          `transaction` = JSON_MODIFY(`transaction`, 'append $', JSON_QUERY(N'$transactionJson')),
          `total_amount` = `total_amount` + :amount
        WHERE `account_number` = :account_number
SQL;
        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue('account_number', $accountNumber);
        $stmt->bindValue('amount', $amount);

        $stmt->execute();
    }

}