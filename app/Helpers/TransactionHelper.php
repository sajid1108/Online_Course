<?php

namespace App\Helpers;

use App\Models\Transaction;

class TransactionHelper
{
    public static function generateUniqueTrxId(): string
    {
    $prefix = 'OBITOBWA';
    do {
        $randomString = $prefix . mt_rand(1000, 9999);
    } while (Transaction::where('booking_trx_id', $randomString)->exists());

    return $randomString;
}
}