<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-22
 * Time: 17:22
 */

namespace App\Projection\Transaction;


use App\Projection\Table;
use Doctrine\DBAL\Connection;
use Prooph\EventStore\Projection\AbstractReadModel;

class TransactionReadModel extends AbstractReadModel
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
        $tableName = Table::TRANSACTION;

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
        $tableTransaction = Table::TRANSACTION;

        $sql = "TRUNCATE TABLE $tableTransaction;";

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    public function delete(): void
    {
        $tableTransaction = Table::TRANSACTION;

        $sql = "DROP TABLE $tableTransaction;";

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    protected function deposit(string $accountNumber, float $amount, \DateTimeImmutable $time, float $oldAmount): void
    {
        $this->updateTable($accountNumber, $oldAmount, $amount, 'd', $time);
    }

    protected function withdraw(string $accountNumber, float $amount, \DateTimeImmutable $time, float $oldAmount): void
    {
        $amount = $amount * -1;

        $this->updateTable($accountNumber, $oldAmount, $amount, 'w', $time);
    }

    private function updateTable(string $accountNumber, float $oldAmount, float $amount, string $type, \DateTimeImmutable $time): void
    {
        $newAmount = $oldAmount + $amount;

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