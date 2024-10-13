<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

use App\Config\OrganiserConfig;
use App\Config\DateTimeConfig;

final class OrganiserDto
{
    #[ApiProperty(identifier: true)]

    #[Groups([
        OrganiserConfig::OUTPUT,
        OrganiserConfig::OUTPUT_LIST,
    ])]
    public int $id;

    #[Assert\Length(
        max: OrganiserConfig::NAME_LENGTH,
        groups: [
            OrganiserConfig::VALID,
        ],
    )]
    #[Assert\NotBlank(
        groups: [
            OrganiserConfig::VALID_CREATE,
        ]
    )]
    #[Groups([
        OrganiserConfig::INPUT,
        OrganiserConfig::OUTPUT,
        OrganiserConfig::OUTPUT_LIST,
    ])]
    public string $name;

    #[Assert\Length(
        max: OrganiserConfig::CITY_LENGTH,
        groups: [
            OrganiserConfig::VALID,
        ],
    )]
    #[Assert\NotBlank(
        groups: [
            OrganiserConfig::VALID_CREATE,
        ]
    )]
    #[Groups([
        OrganiserConfig::INPUT,
        OrganiserConfig::OUTPUT,
        OrganiserConfig::OUTPUT_LIST,
    ])]
    public string $city;

    #[Assert\Length(
        max: OrganiserConfig::PHONE_LENGTH,
        groups: [
            OrganiserConfig::VALID,
        ],
    )]
    #[Groups([
        OrganiserConfig::INPUT,
        OrganiserConfig::OUTPUT,
        OrganiserConfig::OUTPUT_LIST,
    ])]
    public ?string $phone = null;

    #[Assert\Length(
        max: OrganiserConfig::DESCRIPTION_LENGTH,
        groups: [
            OrganiserConfig::VALID,
        ],
    )]
    #[Groups([
        OrganiserConfig::INPUT,
        OrganiserConfig::OUTPUT,
        OrganiserConfig::OUTPUT_LIST,
    ])]
    public ?string $description = null;

    #[Context([
        DateTimeNormalizer::FORMAT_KEY => DateTimeConfig::FORMAT,
    ])]
    #[Groups([
        OrganiserConfig::OUTPUT,
        OrganiserConfig::OUTPUT_LIST,
    ])]
    public DateTimeImmutable $createdAt;

    #[Context([
        DateTimeNormalizer::FORMAT_KEY => DateTimeConfig::FORMAT,
    ])]
    #[Groups([
        OrganiserConfig::OUTPUT,
        OrganiserConfig::OUTPUT_LIST,
    ])]
    public DateTimeImmutable $updatedAt;

}
