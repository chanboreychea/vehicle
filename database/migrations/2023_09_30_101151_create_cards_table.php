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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string("firstNameKh", 100);
            $table->string("lastNameKh", 100);
            $table->string("firstName", 100);
            $table->string("lastName", 100);
            $table->string("role", 100);
            $table->string("entityName", 100);
            $table->string("areaCode",100);
            $table->string("phoneNumber", 20)->unique();
            $table->string("address", 255);
            $table->string("img", 255);
            $table->string("description", 255)->nullable();
            $table->boolean("isApprove");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
