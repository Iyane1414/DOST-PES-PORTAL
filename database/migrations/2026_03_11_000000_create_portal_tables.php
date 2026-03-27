<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issuances', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('category');
            $table->date('date');
            $table->string('division');
            $table->string('url')->nullable();
            $table->timestamps();
        });

        Schema::create('materials', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('type');
            $table->date('date');
            $table->string('division');
            $table->string('url')->nullable();
            $table->timestamps();
        });

        Schema::create('divisions', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('head')->nullable();
            $table->timestamps();
        });

        Schema::create('dx_items', function (Blueprint $table): void {
            $table->id();
            $table->string('category');
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('issuance_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->unique();
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('issuance_categories');
        Schema::dropIfExists('dx_items');
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('issuances');
    }
};
