<?php

declare(strict_types=1);

it('returns API health status', function (): void {
    $this->getJson('/api/health')
        ->assertOk()
        ->assertJsonPath('data.status', 'ok')
        ->assertJsonPath('data.service', 'medical-courses-api');
});
