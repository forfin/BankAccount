<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-17
 * Time: 11:37
 */

namespace App\Projection\Account;


use App\Model\Account\Event\AccountWasDeposit;
use App\Model\Account\Event\AccountWasRegistered;
use App\Model\Account\Event\AccountWasWithdraw;
use Prooph\Bundle\EventStore\Projection\ReadModelProjection;
use Prooph\EventStore\Projection\ReadModelProjector;

final class AccountProjection implements ReadModelProjection
{
    public function project(ReadModelProjector $projector): ReadModelProjector
    {
        $projector->fromStream('event_stream')
            ->when([
                AccountWasRegistered::class => function ($state, AccountWasRegistered $event) {
                    if (! isset($state[$event->aggregateId()]['balance'])) {
                        $state[$event->aggregateId()]['balance'] = 0;
                    }
                    /**
                     * @var AccountReadModel $readModel
                     */
                    $readModel = $this->readModel();
                    $readModel->stack('insert', [
                        'account_number' => $event->accountNumber(),
                        'name' => $event->name(),
                        'total_amount' => 0,
                    ]);

                    return $state;
                },
                AccountWasDeposit::class => function ($state, AccountWasDeposit $event) {
                    if (! isset($state[$event->aggregateId()]['balance'])) {
                        $state[$event->aggregateId()]['balance'] = 0;
                    }
                    /**
                     * @var AccountReadModel $readModel
                     */
                    $readModel = $this->readModel();
                    $readModel->stack('deposit', $event->accountNumber(), $event->amount(), $event->createdAt(), $state[$event->aggregateId()]['balance']);

                    $state[$event->aggregateId()]['balance'] += $event->amount();

                    return $state;
                },
                AccountWasWithdraw::class => function ($state, AccountWasWithdraw $event) {
                    if (! isset($state[$event->aggregateId()]['balance'])) {
                        $state[$event->aggregateId()]['balance'] = 0;
                    }
                    /**
                     * @var AccountReadModel $readModel
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