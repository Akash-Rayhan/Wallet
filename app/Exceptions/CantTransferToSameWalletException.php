<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class CantTransferToSameWalletException extends Exception
{
    public function report(){
        Log::debug('Cannot transfer balance between same wallet');
    }
}
