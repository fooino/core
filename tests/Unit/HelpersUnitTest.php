<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tests\TestCase;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use stdClass;

class HelpersUnitTest extends TestCase
{
    public function test_empty_to_null_or_value()
    {
        $this->assertTrue(emptyToNullOrValue([]) == null);
        $this->assertTrue(emptyToNullOrValue('') == null);
        $this->assertTrue(emptyToNullOrValue('      ') == null);
        $this->assertTrue(emptyToNullOrValue('  "" ') == null);
        $this->assertTrue(emptyToNullOrValue('  " ') == null);
        $this->assertTrue(emptyToNullOrValue("  ' ") == null);
        $this->assertTrue(emptyToNullOrValue("  '' ") == null);
        $this->assertTrue(emptyToNullOrValue("  ' \" ' ") == null);
        $this->assertTrue(emptyToNullOrValue(null) == null);
        $this->assertTrue(emptyToNullOrValue('null') == null);
        $this->assertTrue(emptyToNullOrValue('NULL') == null);
        $this->assertTrue(emptyToNullOrValue('NULl') == null);
        $this->assertTrue(emptyToNullOrValue(collect([])) == null);
        $this->assertTrue(emptyToNullOrValue('foobar') == 'foobar');
        $this->assertTrue(emptyToNullOrValue(1) == 1);
        $this->assertTrue(emptyToNullOrValue(0) == 0);
        $this->assertTrue(emptyToNullOrValue(0.0) == 0.0);
        $this->assertTrue(emptyToNullOrValue("0") == "0");
        $this->assertTrue(emptyToNullOrValue("0.0") == "0.0");
        $this->assertTrue(emptyToNullOrValue(1.1) == 1.1);
        $this->assertTrue(emptyToNullOrValue(true) == true);
        $this->assertTrue(emptyToNullOrValue(false) == false);
        $this->assertTrue(emptyToNullOrValue([1, 'foobar', true]) == [1, 'foobar', true]);
        $this->assertTrue(emptyToNullOrValue(collect([1, 'foobar', true])) == collect([1, 'foobar', true]));
    }

    public function test_zero_to_null_or_value()
    {
        $this->assertTrue(zeroToNullOrValue([]) == null);
        $this->assertTrue(zeroToNullOrValue('') == null);
        $this->assertTrue(zeroToNullOrValue('      ') == null);
        $this->assertTrue(zeroToNullOrValue('  "" ') == null);
        $this->assertTrue(zeroToNullOrValue('  " ') == null);
        $this->assertTrue(zeroToNullOrValue("  ' ") == null);
        $this->assertTrue(zeroToNullOrValue("  '' ") == null);
        $this->assertTrue(zeroToNullOrValue("  ' \" ' ") == null);
        $this->assertTrue(zeroToNullOrValue(null) == null);
        $this->assertTrue(zeroToNullOrValue('null') == null);
        $this->assertTrue(zeroToNullOrValue('NULL') == null);
        $this->assertTrue(zeroToNullOrValue('NULl') == null);
        $this->assertTrue(zeroToNullOrValue(collect([])) == null);
        $this->assertTrue(zeroToNullOrValue(0) == null);
        $this->assertTrue(zeroToNullOrValue(0.0) == null);
        $this->assertTrue(zeroToNullOrValue("0") == null);
        $this->assertTrue(zeroToNullOrValue("0.0") == null);
        $this->assertTrue(zeroToNullOrValue('foobar') == 'foobar');
        $this->assertTrue(zeroToNullOrValue(1) == 1);
        $this->assertTrue(zeroToNullOrValue(1.1) == 1.1);
        $this->assertTrue(zeroToNullOrValue(true) == true);
        $this->assertTrue(zeroToNullOrValue(false) == false);
        $this->assertTrue(zeroToNullOrValue([1, 'foobar', true]) == [1, 'foobar', true]);
        $this->assertTrue(zeroToNullOrValue(collect([1, 'foobar', true])) == collect([1, 'foobar', true]));
    }

    public function test_remove_comma_helper()
    {
        $stdClass = new stdClass;
        $this->assertTrue(removeComma(123) == 123);
        $this->assertTrue(removeComma(123.11) == 123.11);
        $this->assertTrue(removeComma('123,123') == '123123');
        $this->assertTrue(removeComma('123,test') == '123test');
        $this->assertTrue(removeComma(['123,123', '123,foobar']) == ['123123', '123foobar']);
        $this->assertTrue(removeComma(collect([1, 2])) == collect([1, 2]));
        $this->assertTrue(removeComma($stdClass) == $stdClass);
        $this->assertTrue(removeComma(null) == null);
        $this->assertTrue(removeComma(true) == true);
        $this->assertTrue(removeComma(false) == false);
    }

    public function test_trim_empty_string()
    {
        $stdClass = new stdClass;
        $this->assertTrue(trimEmptyString(12) == 12);
        $this->assertTrue(trimEmptyString(12.12) == 12.12);
        $this->assertTrue(trimEmptyString(true) == true);
        $this->assertTrue(trimEmptyString(false) == false);
        $this->assertTrue(trimEmptyString(null) == null);
        $this->assertTrue(trimEmptyString([1, 2]) == [1, 2]);
        $this->assertTrue(trimEmptyString(collect([1, 2])) == collect([1, 2]));
        $this->assertTrue(trimEmptyString($stdClass) == $stdClass);
        $this->assertTrue(trimEmptyString('foobar') == 'foobar');
        $this->assertTrue(trimEmptyString(' foobar') == 'foobar');
        $this->assertTrue(trimEmptyString('foobar ') == 'foobar');
        $this->assertTrue(trimEmptyString(' foobar ') == 'foobar');
    }

    public function test_remove_empty_string()
    {
        $stdClass = new stdClass;
        $this->assertTrue(removeEmptyString(12) == 12);
        $this->assertTrue(removeEmptyString(12.12) == 12.12);
        $this->assertTrue(removeEmptyString(true) == true);
        $this->assertTrue(removeEmptyString(false) == false);
        $this->assertTrue(removeEmptyString([1, 2]) == [1, 2]);
        $this->assertTrue(removeEmptyString(collect([1, 2])) == collect([1, 2]));
        $this->assertTrue(removeEmptyString($stdClass) == $stdClass);
        $this->assertTrue(removeEmptyString('foobar') == 'foobar');
        $this->assertTrue(removeEmptyString(' foobar') == 'foobar');
        $this->assertTrue(removeEmptyString('foobar ') == 'foobar');
        $this->assertTrue(removeEmptyString(' foobar ') == 'foobar');
        $this->assertTrue(removeEmptyString(' 0912 123 1234 ') == '09121231234');
    }

    public function test_replace_slash_to_dash()
    {
        $object = new stdClass;
        $this->assertTrue(replaceSlashToDash(value: ['hi/hello', 'foobar']) == ['hi-hello', 'foobar']);
        $this->assertTrue(replaceSlashToDash(value: '2023/01/02') == '2023-01-02');
        $this->assertTrue(replaceSlashToDash(value: '') == '');
        $this->assertTrue(replaceSlashToDash(value: 123) == 123);
        $this->assertTrue(replaceSlashToDash(value: 123.123) == 123.123);
        $this->assertTrue(replaceSlashToDash(value: [123]) == [123]);
        $this->assertTrue(replaceSlashToDash(value: collect([123])) == collect([123]));
        $this->assertTrue(replaceSlashToDash(value: $object) == $object);
        $this->assertTrue(replaceSlashToDash(value: null == null));
        $this->assertTrue(replaceSlashToDash(value: true == true));
        $this->assertTrue(replaceSlashToDash(value: false == false));
    }

    public function test_change_percentage_helper()
    {
        $this->assertTrue(changePercentage(from: 200, to: 50) == 75);
        $this->assertTrue(changePercentage(from: 10, to: 3.331) == 66.69);
        $this->assertTrue(changePercentage(from: 200, to: 0) == -100);
        $this->assertTrue(changePercentage(from: 0, to: 200) == 100);
        $this->assertTrue(changePercentage(from: -100, to: 200) == 300);
    }

    public function test_number_with_unit()
    {
        $this->assertEquals(numberWithUnit(null), 0);
        $this->assertEquals(numberWithUnit(1), 1);
        $this->assertEquals(numberWithUnit(1.1), 1.1);

        $this->assertEquals(numberWithUnit(1000), '1 msg.thousand');
        $this->assertEquals(numberWithUnit(10000000.22), '10 msg.million');
        $this->assertEquals(numberWithUnit(20030000.22), '20.03 msg.million');
        $this->assertEquals(numberWithUnit(10000000000.22), '10 msg.billion');
        $this->assertEquals(numberWithUnit(1000000000000000), '1,000 msg.trillion');
    }

    public function test_date_range_function()
    {
        $range = dateRange(from: '2024-02-03', to: '2024-02-10');

        $this->assertTrue($range == [
            '2024-02-03',
            '2024-02-04',
            '2024-02-05',
            '2024-02-06',
            '2024-02-07',
            '2024-02-08',
            '2024-02-09',
            '2024-02-10',
        ]);
    }

    public function test_prettify_canonical_helper()
    {
        $this->assertEquals(
            prettifyCanonical("test / prettify canonical ? %& $ *"),
            "test-/-prettify-canonical-?--&----"
        );
        $this->assertEquals(
            prettifyCanonical("https://google.com"),
            "https://google.com"
        );

        $this->assertTrue(prettifyCanonical('') == null);
        $this->assertTrue(prettifyCanonical(null) == null);
    }

    public function test_prettify_slug_helper()
    {
        $this->assertEquals(
            prettifySlug("test / prettify slug ? %& $ *"),
            "test---prettify-slug---------"
        );

        $this->assertEquals(
            prettifySlug("laravel-tips-for-2025"),
            "laravel-tips-for-2025"
        );

        $this->assertTrue(prettifySlug('') == null);
        $this->assertTrue(prettifySlug(null) == null);
    }

    public function test_set_user_timezone()
    {
        $this->assertTrue(config('user-timezone') == null);
        setUserTimezone('UTC');
        $this->assertTrue(config('user-timezone') == 'UTC');
    }

    public function test_get_user_timezone()
    {
        $this->assertTrue(getUserTimezone() == 'UTC');
        setUserTimezone('Asia/Tehran');
        $this->assertTrue(getUserTimezone() == 'Asia/Tehran');
    }

    public function test_current_date()
    {
        $this->assertTrue(currentDate() == date('Y-m-d H:i:s'));
    }


    public function test_resolve_request()
    {

        $user = new class extends User {};

        $this->assertThrows(
            fn() => resolveRequest(
                request: TestFormRequest::class
            ),
            ValidationException::class,
            'The name field is required'
        );

        $resolved = resolveRequest(
            request: TestFormRequest::class,
            data: [
                'name'  => 'foobar',
                'email' => 'foobar@gmail.com'
            ],
            user: $user
        );

        $this->assertTrue($resolved instanceof TestFormRequest);
        $this->assertTrue($resolved->safe()->name == 'foobar');
        $this->assertTrue($resolved->safe()->email == 'foobar@gmail.com');
        $this->assertTrue($resolved->getUserResolver()() == $user);
    }

    public function test_json_attribute()
    {

        Schema::create('users_table', function (Blueprint $table) {
            $table->id();
            $table->json('info')->nullable();
            $table->timestamps();
        });

        $model = new class extends Model
        {
            protected $guarded = ['id'];

            protected $table = 'users_table';

            public function info(): Attribute
            {
                return jsonAttribute();
            }
        };

        $model->create([
            'info'  => ''
        ]);
        $this->assertTrue($model->find(1)->info == []);
        $this->assertTrue($model->find(1)->getRawOriginal('info') == null);
        $this->assertDatabaseHas('users_table', [
            'id'    => 1,
            'info'  => null
        ]);



        $model->create([
            'info'  => ['foo' => 'bar', 123]
        ]);
        $this->assertTrue($model->find(2)->info == ['foo' => 'bar', 123]);
        $this->assertTrue($model->find(2)->getRawOriginal('info') == json_encode(['foo' => 'bar', 123]));
        $this->assertDatabaseHas('users_table', [
            'id'    => 2,
            'info'  => json_encode(['foo' => 'bar', 123])
        ]);
    }


    public function test_user_info()
    {

        Schema::create('blogs_table', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('creatorable');
            $table->timestamps();
        });

        Schema::create('users_table', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('country_id')->nullable();

            $table->string('country_code')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('full_name')->nullable();

            $table->string('phone_number')->nullable();
            $table->timestamps();
        });

        $blog = new class extends Model {

            protected $guarded = ['id'];

            protected $table = 'blogs_table';

            public function creatorable(): MorphTo
            {
                return $this->morphTo('creatorable');
            }
        };

        $user = new class extends User {

            protected $guarded = ['id'];

            protected $table = 'users_table';
        };

        $type = str(class_basename($user))->camel()->value();

        $user->create([]);

        $blog->create([
            'creatorable_type'  => get_class($user),
            'creatorable_id'    => 1
        ]);

        $b = $blog->with('creatorable')->find(1);
        $this->assertEquals(
            userInfo($b, 'creatorable'),
            [
                'id'                    => 1,
                'country_id'            => 0,
                'full_name'             => '',
                'country_code'          => '',
                'phone_number'          => '',
                'phone_number_original' => '',
                'type'                  => $type,
                'type_translated'       => __('msg.' . $type),
            ]
        );


        $user->create([
            'country_id'    => 105,
            'country_code'  => 'IR',
            'first_name'    => 'foo',
            'last_name'     => 'ino',
            'phone_number'  => '09121231234',
        ]);

        $blog->create([
            'creatorable_type'  => get_class($user),
            'creatorable_id'    => 2
        ]);

        $b = $blog->with('creatorable')->find(2);

        $this->assertEquals(
            userInfo($b, 'creatorable'),
            [
                'id'                    => 2,
                'country_id'            => 105,
                'full_name'             => 'foo ino',
                'country_code'          => 'IR',
                'phone_number'          => '09121231234',
                'phone_number_original' => '09121231234',
                'type'                  => $type,
                'type_translated'       => __('msg.' . $type),
            ]
        );


        $user->find(2)->delete();

        $b = $blog->with('creatorable')->find(2);

        $this->assertEquals(
            userInfo($b, 'creatorable'),
            [
                'id'                    => 0,
                'country_id'            => 0,
                'full_name'             => '',
                'country_code'          => '',
                'phone_number'          => '',
                'phone_number_original' => '',
                'type'                  => '',
                'type_translated'       => __('msg.unknown'),
            ]
        );
    }


    public function test_remove_emoji()
    {
        $stdClass = new stdClass;
        $this->assertTrue(removeEmoji(12) == 12);
        $this->assertTrue(removeEmoji(12.12) == 12.12);
        $this->assertTrue(removeEmoji(true) == true);
        $this->assertTrue(removeEmoji(false) == false);
        $this->assertTrue(removeEmoji([1, 2]) == [1, 2]);
        $this->assertTrue(removeEmoji(collect([1, 2])) == collect([1, 2]));
        $this->assertTrue(removeEmoji($stdClass) == $stdClass);
        $this->assertTrue(removeEmoji('foobar') == 'foobar');
        $this->assertTrue(removeEmoji(' ') == ' ');
        $this->assertTrue(removeEmoji('😀😃😄😁😆😅😂🤣😊😇foobar') == 'foobar');
    }
}


class TestFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => [
                'required',
                'max:255'
            ],
            'email' => [
                'required',
                'email'
            ]
        ];
    }
}
