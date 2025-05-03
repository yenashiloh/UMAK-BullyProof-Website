<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $collection) {
            $collection->id();
            $collection->string('title');
            $collection->text('description')->nullable();
            $collection->json('buttons')->default('[]'); // Array of button objects (e.g., { label, action })
            $collection->string('created_by');
            $collection->string('status')->default('active');
            $collection->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cards');
    }
};
