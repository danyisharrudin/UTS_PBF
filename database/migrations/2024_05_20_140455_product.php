<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name', 255)->nullable(false);
        $table->text('description');
        $table->integer('price')->nullable(false);
        $table->string('image', 255);
        $table->integer('category_id')->unsigned()->nullable(false);
        $table->date ('expired_at')->nullable(false);
        $table->string('modified_by', 255)->nullable(false)->comment('email user');
       $table->timestamps();

    });

    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name', 255)->nullable(false);
        $table->string('email')->nullable(false)->unique();
        $table->string('password')->nullable(false);
        $table->enum('role', ['admin', 'user'])->default('user');
        $table->timestamps();

    });

    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name', 255)->nullable(false);
        $table->timestamps();

    });
}

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('users');
        Schema::dropIfExists('categories');
    }
};
