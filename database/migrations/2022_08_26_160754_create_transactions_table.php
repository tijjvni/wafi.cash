<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from')->index()->nullable()->default(null); 
            $table->unsignedBigInteger('to')->index()->nullable()->default(null); ; 
            $table->integer('amount');
            $table->timestamps();

            $table->foreign('from')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade'); 
            $table->foreign('to')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
