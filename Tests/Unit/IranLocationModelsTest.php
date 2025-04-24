<?php
namespace Denason\IranLocation\Tests\Unit;

use Denason\IranLocation\Models\City;
use Denason\IranLocation\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IranLocationModelsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();


        $this->artisan('db:seed', ['--class' => \Denason\IranLocation\Database\Seeders\IranLocationSeeder::class]);
    }

    public function test_it_belongs_to_a_province()
    {
        $city = City::has('province')->first();
        $province = $city->province;

        $this->assertNotNull($province);
        $this->assertEquals(1, $province->id);
    }


    public function test_it_has_many_cities()
    {
        $province = Province::has('cities')->first();
        $cities = $province->cities;
        $this->assertNotNull($cities);
        $this->assertGreaterThan(0, count($cities));
        $this->assertInstanceOf(City::class, $cities->first());
    }

}
