<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

use App\User;

class CurrencyTest extends TestCase
{

    // public function test_user_can_get_wallet(){
    //     $user = new User;
    //     $userB = new User;
    //     $user->setBalance('NGN',500);
    //     $user->setBalance('GBP',0.5);
    //     dd($user->transfer('USD',1,$userB));
    // }

    public function test_user_can_get_wallet(){
        $user = new User;
        $wallet = $user->getWallet();

        $this->assertIsArray($wallet);
        $this->assertEquals($wallet["GBP"],0);
        $this->assertEquals($wallet["NGN"],0);
        $this->assertEquals($wallet["USD"],0);
        $this->assertEquals($wallet["YUAN"],0);

    }

    public function test_user_can_transfer_ngn(){
        $user = new User;
        $userB = new User;
        $wallet = $user->getWallet();

        $userB = new User;
        $user->setBalance('NGN',500);
        $user->transfer('NGN',250,$userB);

        $this->assertEquals($user->getBalance('NGN'),250);

    }

    public function test_user_cannot_transfer_insufficient_usd_from_ngn(){
        $user = new User;
        $userB = new User;
        $wallet = $user->getWallet();

        $userB = new User;
        $user->setBalance('NGN',500);

        $this->assertEquals($user->transfer('USD',2,$userB),"Insufficient Balance");

    }

    public function test_user_can_transfer_usd_from_ngn(){
        $user = new User;
        $userB = new User;
        $wallet = $user->getWallet();

        $userB = new User;
        $NGNAmount = 500;

        $user->setBalance('NGN',$NGNAmount);
        $user->transfer('USD',1,$userB);

        $this->assertEquals($NGNAmount - $user->currencies['NGN'],$user->getBalance("NGN"));
    }


    public function test_user_can_transfer_usd_from_ngn_and_gbp(){
        $user = new User;
        $userB = new User;
        $wallet = $user->getWallet();

        $userB = new User;
        $NGNAmount = 400;
        $GBPAmount = 1;

        $user->setBalance('GBP',$GBPAmount);
        $user->setBalance('NGN',$NGNAmount);
        $user->transfer('USD',2,$userB);

        $this->assertEquals($user->getBalance('GBP'), 0);
        $this->assertLessThan($NGNAmount,$user->getBalance('NGN'),);

    }

}
