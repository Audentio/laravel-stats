<?php

use Audentio\LaravelBase\Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddContentTypeAndContentIdToDailyStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_stats', function (Blueprint $table) {
            $table->morphsNullable('content', null, 'incr_id');

            if (\Audentio\LaravelStats\LaravelStats::usesUniqueKeyOnDailyStats()) {
                $table->dropUnique(['kind', 'sub_kind', 'date']);
            }
        });
        Schema::table('daily_stats', function (Blueprint $table) {
            if (\Audentio\LaravelStats\LaravelStats::usesUniqueKeyOnDailyStats()) {
                $table->unique(['content_type', 'content_id', 'kind', 'sub_kind', 'date']);
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
        Schema::table('daily_stats', function (Blueprint $table) {
            $table->dropMorphs('content');

            if (\Audentio\LaravelStats\LaravelStats::usesUniqueKeyOnDailyStats()) {
                $table->dropUnique(['content_type', 'content_id', 'kind', 'sub_kind', 'date']);
            }
        });
        Schema::table('daily_stats', function (Blueprint $table) {
            if (\Audentio\LaravelStats\LaravelStats::usesUniqueKeyOnDailyStats()) {
                $table->unique(['kind', 'sub_kind', 'date']);
            }
        });
    }
}
