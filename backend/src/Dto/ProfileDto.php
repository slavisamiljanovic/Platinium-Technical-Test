<?php

declare(strict_types=1);

namespace App\Dto;

use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use App\Config\ProfileConfig;
use App\Config\DateTimeConfig;

final class ProfileDto
{

    #[Groups([
        ProfileConfig::OUTPUT,
    ])]
    public string $username;

    #[Groups([
        ProfileConfig::OUTPUT,
    ])]
    public string $bio;

    #[Groups([
        ProfileConfig::OUTPUT,
    ])]
    public ?string $image = null;

    #[Context([
        DateTimeNormalizer::FORMAT_KEY => DateTimeConfig::FORMAT,
    ])]
    #[Groups([
        ProfileConfig::OUTPUT,
    ])]
    public DateTimeImmutable $createdAt;

    #[Context([
        DateTimeNormalizer::FORMAT_KEY => DateTimeConfig::FORMAT,
    ])]
    #[Groups([
        ProfileConfig::OUTPUT,
    ])]
    public DateTimeImmutable $updatedAt;

}
