<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Currency;

class UserTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_can_create_user()
    {

        $this->test_can_add_currencies();
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertAuthenticated();
    }


    public function test_user_can_check_balance()
    {
        // $this->test_can_add_currencies();
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->getJson(route('users.checkBalance'))
        ->assertStatus(200)
        ->assertJson([
            'id' => $user->id,
            'email' => $user->email,
            'balance' => $user->balance,
        ]);
    }

    public function test_user_cannot_deposit_money_without_providing_amount()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->postJson(route('users.depositMoney'))
        ->assertStatus(422)
        ->assertJson([
            "errors" => [
                'amount' => ["The amount field is required."],
            ]
        ]);
    }

    public function test_user_can_deposit_money()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $depositAmount = $this->faker->randomNumber(4, true);

        $this->postJson(route('users.depositMoney'),[
            'amount' => $depositAmount
        ])
        ->assertStatus(200)
        ->assertJson([
            "id" => $user->id,
            "email" => $user->email,
            "balance" => $user->balance + $depositAmount,
        ]);

    }

    public function test_user_cannot_transfer_money_without_providing_amount_and_receiver()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->postJson(route('users.transferMoney'))
        ->assertStatus(422)
        ->assertJson([
            "errors" => [
                'amount' => ["The amount field is required."],
                'receiver' => ["The receiver field is required."],
            ]
        ]);
    }

    public function test_user_cannot_transfer_money_to_non_app_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->postJson(route('users.transferMoney'),[
            'amount' => $this->faker->randomNumber(4, true),
            'receiver' => $this->faker->randomNumber(4, true),
        ])
        ->assertStatus(422)
        ->assertJson([
            "errors" => [
                'receiver' => ["The selected receiver is invalid."],
            ]
        ]);
    }

    public function test_user_cannot_transfer_money_morethan_current_balance()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $receiver = User::factory()->create();


        $this->postJson(route('users.transferMoney'),[
            'amount' => $this->faker->randomNumber(4, true),
            'receiver' => $receiver->id
        ])
        ->assertStatus(200)
        ->assertJson([
            "message" => "Insufficient balance."
        ]);
    }

    public function test_user_can_transfer_money_another_app_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $transferAmount = $this->faker->randomNumber(4, true);

        $user->balance += $transferAmount;
        $user->save();

        $receiver = User::factory()->create();


        $this->postJson(route('users.transferMoney'),[
            'amount' => $transferAmount,
            'receiver' => $receiver->id
        ])
        ->assertStatus(200)
        ->assertJson([
            "message" => "Transfer was successful."
        ]);
    }

    public function test_user_can_transfer_money_out()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $transferAmount = $this->faker->randomNumber(4, true);

        $user->balance += $transferAmount;
        $user->save();

        $receiver = User::factory()->create();


        $this->postJson(route('users.transferMoneyOut'),[
            'amount' => $transferAmount
        ])
        ->assertStatus(200)
        ->assertJson([
            "message" => "Transfer out was successful."
        ]);
    }


    public function test_example_run_through_of_the_app(){

        // User A is added to the app
        $userA = User::factory()->create();


        // User A deposits 10 dollars
        $this->actingAs($userA);
        $depositAmount = 10;

        $this->postJson(route('users.depositMoney'),[
            'amount' => $depositAmount
        ])
        ->assertStatus(200)
        ->assertJson([
            "id" => $userA->id,
            "email" => $userA->email,
            "balance" => $depositAmount,
        ]);
        $userA->refresh();


        // User B is added to the app
        $userB = User::factory()->create();

        // User B deposits 20 dollars
        $this->actingAs($userB);
        $depositAmount = 20;

        $this->postJson(route('users.depositMoney'),[
            'amount' => $depositAmount
        ])
        ->assertStatus(200)
        ->assertJson([
            "id" => $userB->id,
            "email" => $userB->email,
            "balance" => $depositAmount,
        ]);
        $userB->refresh();


        // User B sends 15 dollars to User A
        $transferAmount = 15;

        $this->postJson(route('users.transferMoney'),[
            'amount' => $transferAmount,
            'receiver' => $userA->id
        ])
        ->assertStatus(200)
        ->assertJson([
            "message" => "Transfer was successful."
        ]);
        $userA->refresh();
        $userB->refresh();



        // User A checks their balance and has 25 dollars
        $this->actingAs($userA);

        $this->getJson(route('users.checkBalance'))
        ->assertStatus(200)
        ->assertJson([
            'id' => $userA->id,
            'email' => $userA->email,
            'balance' => 25,
        ]);


        // User B checks their balance and has 5 dollars
        $userB->refresh();
        $this->actingAs($userB);

        $this->getJson(route('users.checkBalance'))
        ->assertStatus(200)
        ->assertJson([
            'id' => $userB->id,
            'email' => $userB->email,
            'balance' => 5,
        ]);


        // User A transfers 25 dollars from their account
        $this->actingAs($userA);
        $transferAmount = 25;

        $this->postJson(route('users.transferMoneyOut'),[
            'amount' => $transferAmount,
        ])
        ->assertStatus(200)
        ->assertJson([
            "message" => "Transfer out was successful."
        ]);
        $userA->refresh();


        // User A checks their balance and has 0 dollars
        $this->actingAs($userA);

        $this->getJson(route('users.checkBalance'))
        ->assertStatus(200)
        ->assertJson([
            'id' => $userA->id,
            'email' => $userA->email,
            'balance' => 0,
        ]);

    }

}
