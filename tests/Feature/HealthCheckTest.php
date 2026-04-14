<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_endpoint_returns_project_status(): void
    {
        DB::shouldReceive('select')
            ->once()
            ->with('SELECT 1')
            ->andReturn([(object) ['ok' => 1]]);

        $response = $this->getJson('/health');

        $response->assertOk()->assertJsonStructure([
            'status',
            'app',
            'environment',
            'timestamp',
            'checks' => [
                'database',
            ],
        ]);
    }
}
