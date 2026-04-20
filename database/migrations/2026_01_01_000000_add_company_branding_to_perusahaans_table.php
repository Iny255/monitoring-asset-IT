<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('perusahaans', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('kode_perusahaan');
            $table->string('primary_color')->default('#007bff')->after('logo');
            $table->string('secondary_color')->nullable()->after('primary_color');
        });
    }

    public function down()
    {
        Schema::table('perusahaans', function (Blueprint $table) {
            $table->dropColumn(['logo', 'primary_color', 'secondary_color']);
        });
    }
};
?>

