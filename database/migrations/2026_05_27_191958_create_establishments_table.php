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
        Schema::create('establishments', function (Blueprint $table) {
            $table->id();


            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->string('name');
            $table->text('search_keywords')->nullable();
            $table->string('type');
            $table->string('phone')->nullable();
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('average_check')->default(0);


            $table->boolean('has_wifi')->default(false);
            $table->boolean('has_terrace')->default(false);
            $table->boolean('is_pet_friendly')->default(false);
            $table->boolean('laptop_friendly')->default(false);


            $table->boolean('is_approved')->default(true);
            $table->string('menu_pdf')->nullable();


            $table->string('opening_time')->default('09:00');
            $table->string('closing_time')->default('22:00');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establishments');
    }
};
