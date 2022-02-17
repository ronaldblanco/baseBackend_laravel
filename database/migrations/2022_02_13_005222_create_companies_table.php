<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->integer('tax')->nullable();
            $table->integer('commission')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('website')->nullable();
            $table->longText('address')->nullable();
            $table->string('smtpserver')->nullable();
            $table->string('smtpsecure')->nullable();
            $table->string('smtpport')->nullable();
            $table->string('smtpuser')->nullable();
            $table->string('smtppass')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
