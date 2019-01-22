<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-22
 * Time: 17:34
 */

namespace App\Projection\Transaction;


use App\Model\Account\Event\AccountWasDeposit;
use App\Model\Account\Event\AccountWasWithdraw;
use Prooph\Bundle\EventStore\Projection\ReadModelProjection;
use Prooph\EventStore\Projection\ReadModelProjector;

class TransactionProjection implements ReadModelProjection
{
    public function project(ReadModelProjector $projector): ReadModelProjector
    {
        $projector->fromStream('event_stream')
            ->when([
                AccountWasDeposit::class => function (array $state, AccountWasDeposit $event) {
                    if (! isset($state[$event->aggregateId()]['balance'])) {
                        $state[$event->aggregateId()]['balance'] = 0;
                    }
                    /**
                     * @var TransactionReadModel $readModel
                     */
                    $readModel = $this->readModel();
                    $readModel->stack('deposit', $event->accountNumber(), $event->amount(), $event->createdAt(), $state[$event->aggregateId()]['balance']);

                    $state[$event->accountNumber()]['balance'] += $event->amount();

                    return $state;
                },
                AccountWasWithdraw::class => function (array $state, AccountWasWithdraw $event) {
                    if (! isset($state[$event->aggregateId()]['balance'])) {
                        $state[$event->aggregateId()]['balance'] = 0;
                    }
                    /**
                     * @var TransactionReadModel $readModel
                     */
                    $readModel = $this->readModel();
                    $readModel->stack('withdraw', $event->accountNumber(), $event->amount(), $event->createdAt(), $state[$event->aggregateId()]['balance']);

                    $state[$event->aggregateId()]['balance'] -= $event->amount();

                    return $state;
                }
            ]);

        return $projector;
    }
}