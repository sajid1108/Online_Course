<?php

namespace App\Observers;

use App\Helpers\TransactionHelper;
use App\Models\Transaction;

class TransactionObserver
{
    //

    public function creating($transaction)
    {
        $transaction->booking_trx_id = TransactionHelper::generateUniqueTrxId();
    }

    public function created(Transaction $transaction): void
    {

    }

    public function updated(Transaction $transaction): void
    {
        
    }

    public function deleted(Transaction $transaction): void
    {
        
    }

    public function restored(Transaction $transaction): void
    {
        
    }
}
