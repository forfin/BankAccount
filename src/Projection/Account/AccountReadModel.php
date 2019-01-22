<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-17
 * Time: 12:52
 */

namespace App\Projection\Account;


use App\Model\Account\AccountCollection;
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
  PRIMARY KEY (`account_number`)
);
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
        $tableAccount = Table::ACCOUNT;

        $sql = "TRUNCATE TABLE $tableAccount;";

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    public function delete(): void
    {
        $tableAccount = Table::ACCOUNT;

        $sql = "DROP TABLE $tableAccount;";

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    protected function insert(array $data): void
    {
        $this->connection->insert(Table::ACCOUNT, $data);
    }

    protected function deposit(string $accountNumber, float $amount, \DateTimeImmutable $time, float $oldAmount): void
    {
        $this->updateTable($accountNumber, $oldAmount, $amount);
    }

    protected function withdraw(string $accountNumber, float $amount, \DateTimeImmutable $time, float $oldAmount): void
    {
        $amount = $amount * -1;

        $this->updateTable($accountNumber, $oldAmount, $amount);
    }

    private function updateTable(string $accountNumber, float $oldAmount, float $amount): void
    {
        $tableName = Table::ACCOUNT;

        $newAmount = $oldAmount + $amount;

        $sql = <<<SQL
        UPDATE `{$tableName}` SET 
          `total_amount` = {$newAmount}
        WHERE `account_number` = :account_number;
SQL;

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue('account_number', $accountNumber);

        $stmt->execute();
    }

}