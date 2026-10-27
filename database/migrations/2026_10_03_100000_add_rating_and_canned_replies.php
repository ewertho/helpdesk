<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->nullable();
            $table->string('rating_comment', 500)->nullable();
        });

        Schema::create('canned_replies', function (Blueprint $table) {
            $table->id();
            $table->string('title', 80);
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('canned_replies');
        Schema::table('tickets', fn (Blueprint $table) => $table->dropColumn(['rating', 'rating_comment']));
    }
};
