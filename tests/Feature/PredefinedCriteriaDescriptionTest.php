<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\PredefinedCriteriaService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PredefinedCriteriaDescriptionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that predefined criteria configurations include descriptions for teachers
     */
    public function test_predefined_criteria_includes_description()
    {
        $service = new PredefinedCriteriaService();

        // Test class_attendance_percentage
        $config = $service->getPredefinedCriteriaConfig('class_attendance_percentage', 0.3);

        $this->assertNotNull($config);
        $this->assertArrayHasKey('description', $config);
        $this->assertEquals('Persentase kehadiran siswa di kelas', $config['description']);

        // Test average_score
        $config2 = $service->getPredefinedCriteriaConfig('average_score', 0.4);

        $this->assertNotNull($config2);
        $this->assertArrayHasKey('description', $config2);
        $this->assertEquals('Nilai rata-rata akademik siswa', $config2['description']);

        // Test tuition_payment_delays
        $config3 = $service->getPredefinedCriteriaConfig('tuition_payment_delays', 0.3);

        $this->assertNotNull($config3);
        $this->assertArrayHasKey('description', $config3);
        $this->assertEquals('Status keterlambatan pembayaran SPP', $config3['description']);
    }
}
