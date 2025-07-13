<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Dateable;
use Fooino\Core\Traits\Resourceable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Schema;

class ResourceableTraitUnitTest extends TestCase
{
    private Model|null $model = null;

    public function setUp(): void
    {
        parent::setUp();

        Schema::create('users_table', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        $model = new class extends Model
        {
            use Dateable;

            protected $guarded = ['id'];

            protected $table = 'users_table';


            public function getCreatedAtAgoAttribute()
            {
                return $this->dateAtAgo('created_at');
            }
        };

        $model->create([
            'id' => 1
        ]);

        $this->model = $model;
    }
    public function test_custom_method()
    {
        $resource = new UserResource($this->model->first());

        $resource->custom([
            'message' => 'foo bar'
        ]);

        $this->assertEquals($resource->with['message'], 'foo bar');
    }

    public function test_get_date_method()
    {
        $resource = new UserResource($this->model->first());

        $this->assertEquals($resource->getDates(), $resource->toArray(request())[0]->data);

        $this->assertArrayHasKey('created_at',      $resource->toArray(request())[0]->data);
        $this->assertArrayHasKey('updated_at',      $resource->toArray(request())[0]->data);
        $this->assertArrayHasKey('created_at_tz',   $resource->toArray(request())[0]->data);
        $this->assertArrayHasKey('updated_at_tz',   $resource->toArray(request())[0]->data);
        $this->assertArrayHasKey('created_at_ago',  $resource->toArray(request())[0]->data);
    }

    public function test_get_date_with_ignore_dates_method()
    {
        $resource = new UserResourceWithIgnore($this->model->first());

        $this->assertEquals($resource->getDates(), $resource->toArray(request())[0]->data);

        $this->assertArrayNotHasKey('created_at',       $resource->toArray(request())[0]->data);
        $this->assertArrayNotHasKey('created_at_tz',    $resource->toArray(request())[0]->data);
        $this->assertArrayNotHasKey('created_at_ago',   $resource->toArray(request())[0]->data);
        $this->assertArrayHasKey('updated_at',          $resource->toArray(request())[0]->data);
        $this->assertArrayHasKey('updated_at_tz',       $resource->toArray(request())[0]->data);
    }
}


class UserResource extends JsonResource
{
    use Resourceable;

    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            $this->merge(value: $this->getDates()),
        ];
    }
}

class UserResourceWithIgnore extends JsonResource
{
    use Resourceable;

    public array $ignoreDates = [
        'created_at'
    ];

    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            $this->merge(value: $this->getDates()),
        ];
    }
}
