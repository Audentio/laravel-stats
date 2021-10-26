<?php

use Audentio\LaravelBase\Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateDailyStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_stats', function (Blueprint $table) {
            $table->id();
            $table->string('kind');
            $table->string('sub_kind')->default('');
            $table->double('value');

            $table->timestamp('date')->index();
            $table->timestamps();

            if (\Audentio\LaravelStats\LaravelStats::usesUniqueKeyOnDailyStats()) {
                $table->unique(['kind', 'sub_kind', 'date']);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_stats');
    }
}
