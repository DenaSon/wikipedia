<?php

namespace Denason\IranLocation\Tests\Unit;

use Denason\IranLocation\IranLocationInterface;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;


class IranLocationHelperTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();


        Artisan::call('migrate:fresh');
        Artisan::call('db:seed');
    }


    /** @test */
    public function it_returns_an_instance_of_iran_location_interface()
    {
        $instance = iranLocation();

        $this->assertInstanceOf(IranLocationInterface::class, $instance);
    }

    /** @test */
    public function it_can_get_provinces_through_helper()
    {
        $provinces = iranLocation()->getProvinces();

        $this->assertNotEmpty($provinces);
        $this->assertTrue($provinces->first()->name !== '');
    }

    /** @test */
    public function it_can_get_a_province_by_id()
    {
        $province = iranLocation()->getProvinceById(1);

        $this->assertNotNull($province);
        $this->assertEquals(1, $province->id);
    }

    /** @test */
    public function it_can_get_cities_of_a_province()
    {
        $cities = iranLocation()->getCitiesByProvinceId(1);

        $this->assertNotEmpty($cities);
        $this->assertEquals(1, $cities->first()->province_id);
    }

    /** @test */
    public function it_can_get_all_provinces_with_their_cities()
    {
        $result = iranLocation()->getProvincesWithCities();

        $this->assertNotEmpty($result);
        $this->assertTrue($result->first()->relationLoaded('cities'));
    }
}
