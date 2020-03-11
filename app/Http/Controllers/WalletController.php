<?php

namespace App\Http\Controllers;

use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    private $walletService;
    public function __construct()
    {
        $this->walletService = new WalletService();
    }
    public function transferBalance(Request $request){
        try{
            $transferResponse = $this->walletService->transfer(
                $request->fromSender,
                $request->toreceiver,
                $request->transferAmount
            );
            return response()->json(['success' => true, 'message'=> $transferResponse['message']]);
        }catch(\Exception $e){
            return response()-> json(['success'=> false, 'message' => $e->getMessage()]);
        }
    }
}
