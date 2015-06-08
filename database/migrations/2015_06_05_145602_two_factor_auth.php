<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TwoFactorAuth extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table)
        {
            $table->string('two_factor_enabled')->nullable();
            $table->string('two_factor_code')->nullable();
            $table->string('two_factor_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table)
        {
            $table->dropColumn(
                'two_factor_enabled',
                'two_factor_code',
                'two_factor_phone'
            );
        });
    }

}
