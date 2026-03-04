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

        Schema::create('product_manuals', function (Blueprint $table) use ($isPgsql) {
            $table->id();
            $table->string('product_name');
            $table->string('version');
            $table->string('section');
            $table->string('difficulty');
            $table->text('content');

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
        Schema::dropIfExists('product_manuals');
    }
};
