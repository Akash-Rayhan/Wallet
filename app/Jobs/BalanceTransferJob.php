<?php

namespace App\Jobs;

use App\Services\WalletService;
use App\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class BalanceTransferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $walletService;
    protected $senderWalletId;
    protected $receiverWalletId;
    protected $transferAmount;

    /**
     * Create a new job instance.
     *
     * @param $senderWalletId
     * @param $receiverWalletId
     * @param $transferAmount
     */
    public function __construct($senderWalletId, $receiverWalletId, $transferAmount)
    {
        $this->walletService = new WalletService();
        $this->senderWalletId = $senderWalletId;
        $this->receiverWalletId = $receiverWalletId;
        $this->transferAmount = $transferAmount;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $transferValidationResponse = $this->walletService->balanceTransferValidation(
                $this->senderWalletId,
                $this->receiverWalletId,
                $this->transferAmount);
            if($transferValidationResponse['success']){
                Wallet::where('user_id',$this->senderWalletId)->decrement('balance',$this->transferAmount);
                Wallet::where('user_id',$this->receiverWalletId)->increment('balance',$this->transferAmount);
                DB::commit();
            }else{
                DB::rollBack();
            }
        }catch(\Exception $e){
            DB::rollBack();
        }
    }
}
