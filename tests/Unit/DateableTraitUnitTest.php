<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Dateable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DateableTraitUnitTest extends TestCase
{
    private Collection|null $models = null;
    private Model|null $model = null;
    private Model|null $blankDate = null;
    private Model|null $filledDate = null;
    private string $date = '';
    private string $createdDate = '';
    private string $dateAsTimezone = '';
    private string $humanReadable = '';

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $timezone = 'Asia/Tehran';
        Carbon::setLocale('fa');
        config(['app.locale' => 'fa']);
        setUserTimezone($timezone);

        $this->date = '2022-12-24 09:50:00';
        $this->createdDate = explode(' ', $this->date)[0];
        $this->dateAsTimezone = '1401-10-03 13:20:00';
        $this->humanReadable = Carbon::parse(time: $this->date)->diffForHumans();

        Schema::create('users_table', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('created_date')->nullable();
            $table->timestamp('expire_at')->nullable();
            $table->timestamp('expired_at')->nullable();
        });

        $model = new class extends Model
        {
            use Dateable;

            protected $guarded = ['id'];

            protected $table = 'users_table';
        };

        $model->insert([
            [
                'id'                    => 1,
                'created_at'            => null,
                'created_date'          => null,
                'updated_at'            => null,
                'deleted_at'            => null,
                'expire_at'             => null,
                'expired_at'            => null,
            ],
            [
                'id'                    => 2,
                'created_at'            => $this->date,
                'created_date'          => $this->createdDate,
                'updated_at'            => $this->date,
                'deleted_at'            => $this->date,
                'expire_at'             => $this->date,
                'expired_at'            => $this->date,
            ],
            [
                'id'                    => 3,
                'created_at'            => date('Y-m-d H:i:s'),
                'created_date'          => date('Y-m-d'),
                'updated_at'            => date('Y-m-d H:i:s'),
                'deleted_at'            => date('Y-m-d H:i:s'),
                'expire_at'             => date('Y-m-d H:i:s'),
                'expired_at'            => date('Y-m-d H:i:s'),
            ]
        ]);

        $this->model = $model;
        $this->models = $this->model->get();

        $this->blankDate = collect($this->models)->where('id', 1)->first();
        $this->filledDate = collect($this->models)->where('id', 2)->first();
    }

    public function test_created_at_accessors_attribute()
    {
        $this->assertEquals('', $this->blankDate->created_at);
        $this->assertEquals($this->date, $this->filledDate->created_at);
    }

    public function test_updated_at_accessors_attribute()
    {
        $this->assertEquals('', $this->blankDate->updated_at);
        $this->assertEquals($this->date, $this->filledDate->updated_at);
    }

    public function test_deleted_at_accessors_attribute()
    {
        $this->assertEquals('', $this->blankDate->deleted_at);
        $this->assertEquals($this->date, $this->filledDate->deleted_at);
    }

    public function test_expire_at_accessors_attribute()
    {
        $this->assertEquals('', $this->blankDate->expire_at);
        $this->assertEquals($this->date, $this->filledDate->expire_at);
    }

    public function test_expired_at_accessors_attribute()
    {
        $this->assertEquals('', $this->blankDate->expired_at);
        $this->assertEquals($this->date, $this->filledDate->expired_at);
    }

    public function test_created_at_tz_accessors_attribute()
    {
        $this->assertEquals('', $this->blankDate->created_at_tz);
        $this->assertEquals($this->dateAsTimezone, $this->filledDate->created_at_tz);
    }

    public function test_updated_at_tz_accessors_attribute()
    {
        $this->assertEquals('', $this->blankDate->updated_at_tz);
        $this->assertEquals($this->dateAsTimezone, $this->filledDate->updated_at_tz);
    }

    public function test_deleted_at_tz_accessors_attribute()
    {
        $this->assertEquals('', $this->blankDate->deleted_at_tz);
        $this->assertEquals($this->dateAsTimezone, $this->filledDate->deleted_at_tz);
    }

    public function test_expire_at_tz_accessors_attribute()
    {
        $this->assertEquals('', $this->blankDate->expire_at_tz);
        $this->assertEquals($this->dateAsTimezone, $this->filledDate->expire_at_tz);
    }

    public function test_expired_at_tz_accessors_attribute()
    {
        $this->assertEquals('', $this->blankDate->expired_at_tz);
        $this->assertEquals($this->dateAsTimezone, $this->filledDate->expired_at_tz);
    }

    public function test_created_at_ago_accessors_attribute()
    {
        $this->assertEquals('', $this->blankDate->dateAtAgo('created_at'));
        $this->assertEquals($this->humanReadable, $this->filledDate->dateAtAgo('created_at'));
    }

    public function test_created_at_scope()
    {
        $this->assertTrue($this->model->createdAt(null)->count('id') == 3);
        $this->assertTrue($this->model->createdAt($this->createdDate)->count('id') == 1);
    }

    public function test_created_date_scope()
    {
        $this->assertTrue($this->model->createdDate(null)->count('id') == 3);
        $this->assertTrue($this->model->createdDate($this->createdDate)->count('id') == 1);
    }

    public function test_where_field_between_scope()
    {
        $this->assertTrue($this->model->whereFieldBetween(field: 'id', from: 1, to: 2)->count('id') == 2);
    }

    public function test_today_created_scope()
    {
        $this->assertTrue($this->model->todayCreated()->count('id') == 1);
    }

    public function test_yesterday_created_scope()
    {
        $this->model->find(3)->update([
            'created_at'    => date('Y-m-d H:i:s', strtotime('yesterday'))
        ]);

        $this->assertTrue($this->model->yesterdayCreated()->first()->id == 3);
    }

    public function test_today_created_date_scope()
    {
        $this->assertTrue($this->model->todayCreatedDate()->count('id') == 1);
    }

    public function test_yesterday_created_date_scope()
    {
        $this->model->find(3)->update([
            'created_date'    => date('Y-m-d', strtotime('yesterday'))
        ]);

        $this->assertTrue(filled($this->model->yesterdayCreatedDate()->first()));
    }


    public function test_this_year_created_scope()
    {
        $this->assertTrue($this->model->thisYearCreated()->count('id') == 1);
        $this->assertTrue($this->model->thisYearCreatedDate()->count('id') == 1);
    }

    public function test_last_30_days_created_scope()
    {
        $this->assertTrue($this->model->last30DaysCreated()->count('id') == 1);
        $this->assertTrue($this->model->last30DaysCreatedDate()->count('id') == 1);
    }

    public function test_this_month_created_scope()
    {
        $this->assertTrue($this->model->thisMonthCreated()->count('id') == 1);
        $this->assertTrue($this->model->thisMonthCreatedDate()->count('id') == 1);
    }

    public function test_where_date_between_scope()
    {
        $this->assertTrue($this->model->whereDateBetween(field: 'created_at', from: date('Y-m-d', strtotime('yesterday')), to: date('Y-m-d', strtotime('tomorrow')))->count('id') == 1);
    }
}
