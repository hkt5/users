<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() : void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('login_attemps')->default(0);
            $table->boolean('is_confirmed')->default(false);
            $table->timestamp('last_password_changed');
            $table->timestamp('expired_token')->nullable();
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('role_id');
            $table->foreign('status_id')->references('id')->on('statuses')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('role_id')->references('id')->on('roles')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        Schema::dropIfExists('users');
    }
};
