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
        Schema::connection('mongodb')->create('form_elements', function (Blueprint $table) {
            $table->id();
            $table->string('form_builder_id');
            $table->string('step_id');
            $table->string('element_type'); // section, paragraph, multiple_choice, checkbox, dropdown, file_upload
            $table->string('title')->nullable();
            $table->integer('position')->default(0);
            $table->json('settings')->nullable();
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(false);
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
        Schema::connection('mongodb')->dropIfExists('form_elements');
    }
};
