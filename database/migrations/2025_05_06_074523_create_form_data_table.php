<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::connection($this->connection)->create('form_data', function (Blueprint $collection) {
            // Primary identifiers
            $collection->string('_id');
            $collection->string('form_builder_id');
            
            // Steps data structure
            $collection->array('steps_data');
            
            // List of completed steps
            $collection->array('completed_steps');
            
            // Metadata
            $collection->string('reported_by');
            $collection->string('status');
            $collection->boolean('validated');
            $collection->dateTime('reported_at');
            $collection->dateTime('last_modified_at');

            // Indexes for improved query performance
            $collection->index('form_builder_id');
            $collection->index('reported_by');
            $collection->index('status');
            $collection->index('reported_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('form_data');
    }
};