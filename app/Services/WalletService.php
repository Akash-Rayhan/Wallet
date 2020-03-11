<?php


namespace App\Services;


use App\Jobs\BalanceTransferJob;
use App\Wallet;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function  transfer(int $senderWalletId, int $receiverWalletId, float $transferAmount) : array {
        try{
            dispatch(new BalanceTransferJob($senderWalletId,$receiverWalletId,$transferAmount))
                ->onQueue('transfer-balance')
                ->delay(Carbon::now()->addseconds(10));
            return ['success'=> true, 'message'=>['Balance has been transferred']];
        }catch (\Exception $e){
            return ['success' => false, 'message' => [$e->getMessage()]];
        }
    }
    public function balanceTransferValidation(int $senderWalletId, int $receiverWalletId, float $transferAmount) :array{
        $exceptionMessages = [];
        $senderWallet = Wallet::where('id', $senderWalletId)->first();
        $receiverWallet = Wallet::where('id', $receiverWalletId)->first();
        if(isset($senderWallet) && isset($receiverWallet) && $senderWallet->balance >= $transferAmount){
            return ['success' => true, 'message' => 'Balance transfer is possible'];
        }else{
            if (!isset($senderWallet)){
                array_push($exceptionMessages, 'Sender Wallet not found');
            }else{
                if($senderWallet->balance < $transferAmount){
                    array_push($exceptionMessages, 'Not enough balance in sender wallet');
                }
            }
            if (!isset($receiverWallet)){
                array_push($exceptionMessages, 'Receiver Wallet not found ');
            }
            return ['success' => false, 'message' => $exceptionMessages];
        }
    }
}
