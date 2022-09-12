<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Currency;
use App\Models\Transaction;

use App\Http\Requests\DepositMoneyRequest;
use App\Http\Requests\TransferMoneyRequest;
use App\Http\Requests\TransferMoneyOutRequest;

use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    //
    public function checkBalance(){

        return response()->json(
            auth()->user()
        );
    }


    public function depositMoney(DepositMoneyRequest $request){


        $depositMoney = DB::transaction(function() use ($request){
            
            $user = User::find(auth()->id());
            $user->balance += $request->amount;
            $user->save();

            $transaction = new Transaction;
            $transaction->to = $user->id;
            $transaction->amount = $request->amount;
            $transaction->save();

            return $user;

        });

        return response()->json(
            $depositMoney
        );

    }

    public function transferMoney(TransferMoneyRequest $request){

        $user = User::find(auth()->id());

        if($request->amount > $user->balance){
            return response()->json([
                "message" => "Insufficient balance."
            ]
            ,200);
        }

        $transferMoney = DB::transaction(function() use ($request){
        
            $user = User::find(auth()->id());
            $user->balance -= $request->amount;
            $user->save();

            $receiver = User::find($request->receiver);
            $receiver->balance += $request->amount;
            $receiver->save();

            $transaction = new Transaction;
            $transaction->from = $user->id;
            $transaction->to = $receiver->id;
            $transaction->amount = $request->amount;
            $transaction->save();

            return true;

        });

        if($transferMoney){
            return response()->json([
                "message" => "Transfer was successful."
            ]);
        }

        return response()->json([
            "message" => "An error occurred."
        ]);


    }

    public function transferMoneyOut(TransferMoneyOutRequest $request){

        $user = User::find(auth()->id());

        if($request->amount > $user->balance){
            return response()->json([
                "message" => "Insufficient balance."
            ]
            ,200);
        }

        $transferMoney = DB::transaction(function() use ($request){
        
            $user = User::find(auth()->id());
            $user->balance -= $request->amount;
            $user->save();

            $transaction = new Transaction;
            $transaction->from = $user->id;
            $transaction->amount = $request->amount;
            $transaction->save();

            return true;

        });

        if($transferMoney){
            return response()->json([
                "message" => "Transfer out was successful."
            ]);
        }

        return response()->json([
            "message" => "An error occurred."
        ]);


    }

    public function getCurrencies()
    {
        return response()->json([
            "message" => "Retreiving all currencies.",
            "data" => Currency::all()
        ]
        ,200);
    }

}
