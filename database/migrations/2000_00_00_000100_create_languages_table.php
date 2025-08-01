<?php

use Fooino\Core\Enums\Direction;
use Fooino\Core\Enums\LanguageState;
use Fooino\Core\Enums\LanguageStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {

            $table->tinyIncrements('id');

            $table->string('name', 100);
            $table->char('code', 10)->unique();

            $table->enum('direction', Direction::values())->default(Direction::LTR->value);
            $table->enum('status', LanguageStatus::values())->default(LanguageStatus::INACTIVE->value)->index();
            $table->enum('state', LanguageState::values())->default(LanguageState::NON_DEFAULT->value);
            $table->bigInteger('priority')->default(0);

            $table->json('timezones')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
