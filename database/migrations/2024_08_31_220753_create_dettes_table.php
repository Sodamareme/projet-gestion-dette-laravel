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
        Schema::create('dettes', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 10, 2);
            $table->decimal('montantDu', 8, 2)->nullable();;
            $table->decimal('montantRestant', 10, 2);
            $table->unsignedBigInteger('idClient');
            $table->timestamps();
            
            // Add foreign key if needed
            $table->foreign('idClient')->references('id')->on('clients')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('dettes');
    }
    
};
