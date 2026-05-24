<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('color', 20)->default('#0ea5e9');
            $table->string('icon', 40)->default('receipt');
            $table->boolean('is_default')->default(true);
            $table->timestamps();
        });

        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('name', 140);
            $table->string('type', 40)->default('trip');
            $table->string('currency', 8)->default('INR');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 30)->default('member');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['group_id', 'user_id']);
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 160);
            $table->decimal('amount', 12, 2);
            $table->string('split_type', 24)->default('equal');
            $table->date('expense_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['group_id', 'expense_date']);
        });

        Schema::create('expense_payers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('paid_amount', 12, 2);
            $table->timestamps();
        });

        Schema::create('expense_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('share_amount', 12, 2);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('paid_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('paid_to')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->timestamp('settled_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlements');
        Schema::dropIfExists('expense_splits');
        Schema::dropIfExists('expense_payers');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('group_members');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('categories');
    }
};
