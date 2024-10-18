<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\Profile;

class ProfileTest extends ApiResourceTestCase
{

    private function assertMatchesProfileJsonSchema(string $operationName): void
    {
        $this->assertMatchesApiResourceJsonSchema(Profile::class, 'profile_' . $operationName);
    }

    /**
     * @param string $method
     * @param string $profileApi
     * @param string $token
     * @return mixed[]
     */
    private function requestProfiles(string $method, string $profileApi, string $token = ''): array
    {
        /** @var mixed[][] */
        $result = $this->requestApi($method, 'profiles/' . $profileApi, token: $token);
        return $result['profile'] ?? [];
    }

    public function testGet(): void
    {
        $username = 'user1';

        $profile = $this->requestProfiles('GET', $username);

        $this->assertMatchesProfileJsonSchema('get');

        $this->assertEquals($username, $profile['username']);
        $this->assertEquals('bio 1', $profile['bio']);
        $this->assertEquals('image-1.png', $profile['image']);
    }

}
