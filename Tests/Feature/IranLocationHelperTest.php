<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use \IranLocation;
use Denason\IranLocation\Models\Province;
use Denason\IranLocation\Models\City;

class IranLocationHelperTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed initial data
        $this->artisan('db:seed', ['--class' => \Denason\IranLocation\Database\Seeders\IranLocationSeeder::class]);
    }

    public function test_get_provinces_returns_collection()
    {
        $provinces = IranLocation::getProvinces();
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $provinces);
        $this->assertNotEmpty($provinces);
    }

    public function test_get_province_by_id_returns_correct_province()
    {
        $province = Province::first();
        $result = IranLocation::getProvinceById($province->id);
        $this->assertEquals($province->id, $result->id);
    }

    public function test_get_province_by_name_returns_correct_province()
    {
        $province = Province::first();
        $result = IranLocation::getProvinceByName($province->name);
        $this->assertEquals($province->name, $result->name);
    }

    public function test_get_provinces_with_cities_has_cities()
    {
        $provinces = IranLocation::getProvincesWithCities();
        $this->assertNotEmpty($provinces);
        $this->assertTrue($provinces->first()->relationLoaded('cities'));
    }

    public function test_get_cities_returns_collection()
    {
        $cities = IranLocation::getCities();
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $cities);
        $this->assertNotEmpty($cities);
    }

    public function test_get_cities_by_province_id()
    {
        $province = Province::first();
        $cities = IranLocation::getCitiesByProvinceId($province->id);
        $this->assertNotEmpty($cities);
        $this->assertEquals($province->id, $cities->first()->province_id);
    }

    public function test_get_cities_by_province_name()
    {
        $province = Province::first();
        $cities = IranLocation::getCitiesByProvinceName($province->name);
        $this->assertNotEmpty($cities);
        $this->assertEquals($province->id, $cities->first()->province_id);
    }

    public function test_get_city_by_id()
    {
        $city = City::first();
        $result = IranLocation::getCityById($city->id);
        $this->assertEquals($city->name, $result->name);
    }

    public function test_get_city_by_name()
    {
        $city = City::first();
        $result = IranLocation::getCityByName($city->name);
        $this->assertEquals($city->id, $result->id);
    }

    public function test_get_province_of_city_id()
    {
        $city = City::first();
        $province = IranLocation::getProvinceOfCityId($city->id);
        $this->assertEquals($city->province_id, $province->id);
    }

    public function test_get_province_of_city_name()
    {
        $city = City::first();
        $province = IranLocation::getProvinceOfCityName($city->name);
        $this->assertEquals($city->province_id, $province->id);
    }
}
