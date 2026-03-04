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
        $isPgsql = Schema::getConnection()->getDriverName() === 'pgsql';

        if ($isPgsql) {
            Schema::ensureVectorExtensionExists();
        }

        Schema::create('support_faqs', function (Blueprint $table) use ($isPgsql) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category');
            $table->string('product_line');
            $table->string('priority');

            if ($isPgsql) {
                $table->vector('embedding', dimensions: 1536)->nullable()->index();
            } else {
                $table->json('embedding')->nullable();
            }

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_faqs');
    }
};
