<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    /**
     * Reverse the migrations.
     */
    public function up()
    {
        Schema::table('dettes', function (Blueprint $table) {
            $table->decimal('montantDu', 8, 2)->after('montant'); // Exemple de type
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dettes', function (Blueprint $table) {
            $table->dropColumn('montantDu');
        });
    }
};
