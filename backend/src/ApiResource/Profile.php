<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use Symfony\Component\Serializer\Annotation\Groups;

use App\Config\ProfileConfig;
use App\Dto\ProfileDto;
use App\State\ProfileProvider;

#[ApiResource(
    operations: [
        new Get(
            name: 'profile_get',
            uriTemplate: '/profiles/{username}',
            uriVariables: [
                'username' => new Link(
                    fromClass: ProfileDto::class,
                    fromProperty: 'username'
                ),
            ],
            provider: ProfileProvider::class,
            normalizationContext: [
                'groups' => [
                    ProfileConfig::OUTPUT,
                ],
                'skip_null_values' => false,
            ],
            openapiContext: [
                'summary'     => 'Get user profile.',
                'description' => 'Get user profile.',
            ],
        ),
    ],
)]

final class Profile
{
    #[Groups([
        ProfileConfig::OUTPUT,
    ])]
    public ?ProfileDto $profile = null;
}
