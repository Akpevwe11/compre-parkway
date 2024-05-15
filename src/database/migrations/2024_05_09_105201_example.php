<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examples', function (Blueprint $table) {
            $table->id();
            $table->morphs('exampleable');
            $table->boolean('is_primary');
            $table->string('image_uuid')->unique('image_uuid');
            $table->json('response_payload')->index('response_payload');
            $table->string('image_path')->nullable();
            $table->string('provider');
            $table->string('storage_driver')->nullable();
            $table->string('similarity_score')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('example');
    }
};
