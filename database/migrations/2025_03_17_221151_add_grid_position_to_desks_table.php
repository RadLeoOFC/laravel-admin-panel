<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('desks', function (Blueprint $table) {
            // Удалим старые координаты, если они вдруг остались
            if (Schema::hasColumn('desks', 'grid_column')) {
                $table->dropColumn('grid_column');
            }
            if (Schema::hasColumn('desks', 'grid_row')) {
                $table->dropColumn('grid_row');
            }

            // Добавим координаты, как в restaurant-проекте
            $table->integer('coordinates_x')->after('status')->nullable();
            $table->integer('coordinates_y')->after('coordinates_x')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desks', function (Blueprint $table) {
            $table->dropColumn(['coordinates_x', 'coordinates_y']);
        });
    }
};
