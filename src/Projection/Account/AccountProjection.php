<?php
/**
 * BankAccount
 * User: poraphitchuesook
 * Date: 2019-01-17
 * Time: 11:37
 */

namespace App\Projection\Account;


use App\Model\Account\Event\AccountWasRegistered;
use Prooph\Bundle\EventStore\Projection\ReadModelProjection;
use Prooph\EventStore\Projection\ReadModelProjector;

final class AccountProjection implements ReadModelProjection
{
    public function project(ReadModelProjector $projector): ReadModelProjector
    {
        $projector->fromStream('event_stream')
            ->when([
                AccountWasRegistered::class => function ($state, AccountWasRegistered $event) {
                    /**
                     * @var AccountReadModel $readModel
                     */
                    $readModel = $this->readModel();
                    $readModel->stack('insert', [
                        'account_number' => $event->accountNumber(),
                        'name' => $event->name(),
                    ]);
                }
            ]);

        return $projector;
    }
}