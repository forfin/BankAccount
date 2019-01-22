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

    /**
     * @var AccountFinder
     */
    private $accountFinder;

    public function __construct(Connection $connection, AccountFinder $accountFinder)
    {
        $this->connection = $connection;
        $this->accountFinder = $accountFinder;
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

        $tableName = Table::TRANSACTION;

        $sql = <<<EOT
CREATE TABLE `$tableName` (
  `entity_id` int NOT NULL AUTO_INCREMENT,
  `account_number` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `old_amount` real COLLATE utf8_unicode_ci NOT NULL,
  `adjusting_amount` real COLLATE utf8_unicode_ci NOT NULL,
  `new_amount` real COLLATE utf8_unicode_ci NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`entity_id`)
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
        $tableTransaction = Table::TRANSACTION;

        $sql = "TRUNCATE TABLE $tableAccount;";
        $sql .= "TRUNCATE TABLE $tableTransaction;";

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    public function delete(): void
    {
        $tableAccount = Table::ACCOUNT;
        $tableTransaction = Table::TRANSACTION;

        $sql = "DROP TABLE $tableAccount;";
        $sql .= "DROP TABLE $tableTransaction;";

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    protected function insert(array $data): void
    {
        $this->connection->insert(Table::ACCOUNT, $data);
    }

    protected function deposit(string $accountNumber, float $amount, \DateTimeImmutable $time): void
    {
        $account = $this->accountFinder->findByAccountNumber($accountNumber);

        $oldAmount = $account->total_amount;

        $this->updateTable($accountNumber, $oldAmount, $amount, 'd', $time);
    }

    protected function withdraw(string $accountNumber, float $amount, \DateTimeImmutable $time): void
    {
        $account = $this->accountFinder->findByAccountNumber($accountNumber);

        $oldAmount = $account->total_amount;

        $amount = $amount * -1;

        $this->updateTable($accountNumber, $oldAmount, $amount, 'w', $time);
    }

    private function updateTable(string $accountNumber, float $oldAmount, float $amount, string $type, \DateTimeImmutable $time): void
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

        $this->connection->insert(Table::TRANSACTION, [
            'account_number' => $accountNumber,
            'type' => $type,
            'old_amount' => $oldAmount,
            'adjusting_amount' => $amount,
            'new_amount' => $newAmount,
            'time' => $time->format('Y-m-d H:i:s'),
        ]);
    }

}