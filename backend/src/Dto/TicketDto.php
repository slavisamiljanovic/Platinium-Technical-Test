<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

use App\Config\TicketConfig;
use App\Config\EventConfig;
use App\Config\DateTimeConfig;

final class TicketDto
{

    #[ApiProperty(identifier: true)]

    #[Groups([
        TicketConfig::OUTPUT,
        TicketConfig::OUTPUT_LIST,
    ])]
    public ?int $id = null;

    #[Assert\Length(
        max: TicketConfig::NAME_LENGTH,
        groups: [
            TicketConfig::VALID,
        ],
    )]
    #[Assert\NotBlank(
        groups: [
            TicketConfig::VALID_CREATE,
        ]
    )]
    #[Groups([
        TicketConfig::INPUT,
        TicketConfig::OUTPUT,
        TicketConfig::OUTPUT_LIST,
    ])]
    public string $name;

    #[Assert\Length(
        max: TicketConfig::DESCRIPTION_LENGTH,
        groups: [
            TicketConfig::VALID,
        ],
    )]
    #[Groups([
        TicketConfig::INPUT,
        TicketConfig::OUTPUT,
        TicketConfig::OUTPUT_LIST,
    ])]
    public ?string $description = null;

    #[Context([
        DateTimeNormalizer::FORMAT_KEY => DateTimeConfig::FORMAT,
    ])]
    #[Groups([
        TicketConfig::OUTPUT,
        TicketConfig::OUTPUT_LIST,
    ])]
    public DateTimeImmutable $createdAt;

    #[Context([
        DateTimeNormalizer::FORMAT_KEY => DateTimeConfig::FORMAT,
    ])]
    #[Groups([
        TicketConfig::OUTPUT,
        TicketConfig::OUTPUT_LIST,
    ])]
    public DateTimeImmutable $updatedAt;

    /**
     * @var EventDto[]
     */
    #[Assert\Valid(
        groups: [
            TicketConfig::VALID_CREATE,
            TicketConfig::VALID_UPDATE,
        ]
    )]
    #[Assert\Type(
        'array',
        groups: [
            TicketConfig::VALID_CREATE,
            TicketConfig::VALID_UPDATE,
        ]
    )]
    #[Assert\All([
        new Assert\Type(
            type: EventDto::class,
            groups: [
                TicketConfig::VALID_CREATE,
                TicketConfig::VALID_UPDATE,
            ]
        )
    ])]
     #[Groups([
        TicketConfig::INPUT,
        TicketConfig::OUTPUT,
        TicketConfig::OUTPUT_LIST,
    ])]
    public array $events = [];

}
