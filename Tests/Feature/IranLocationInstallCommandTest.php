<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class IranLocationInstallCommandTest extends TestCase
{
    /** @test */
    public function it_runs_the_install_command_successfully()
    {
        $configPath = config_path('iran-location.php');
        if (File::exists($configPath)) {
            File::delete($configPath);
        }

        Artisan::call('iran-location:install');
        $output = Artisan::output();
        $this->assertStringContainsString('IranLocation package installed successfully', $output);

        $this->assertFileExists(config_path('iran-location.php'));

        $this->assertTrue(\Schema::hasTable('provinces'));
        $this->assertTrue(\Schema::hasTable('cities'));
    }
}
