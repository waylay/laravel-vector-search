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

        Schema::create('blog_posts', function (Blueprint $table) use ($isPgsql) {
            $table->id();
            $table->string('title');
            $table->string('topic');
            $table->string('audience');
            $table->text('excerpt');
            $table->longText('body');

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
        Schema::dropIfExists('blog_posts');
    }
};
