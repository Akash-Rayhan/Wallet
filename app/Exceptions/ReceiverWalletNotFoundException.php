<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ReceiverWalletNotFoundException extends Exception
{
    public function report(){
        Log::debug('Receiver Wallet not Found!');
    }
}
