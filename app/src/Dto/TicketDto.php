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

    #[Assert\NotNull(
        groups: [
            EventConfig::VALID_CREATE,
            EventConfig::VALID_UPDATE,
        ]
    )]
    #[Groups([
        TicketConfig::OUTPUT,
        TicketConfig::OUTPUT_LIST,
        EventConfig::INPUT,
        EventConfig::OUTPUT,
        EventConfig::OUTPUT_LIST,
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
        EventConfig::INPUT,
        EventConfig::OUTPUT,
        EventConfig::OUTPUT_LIST,
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
        EventConfig::OUTPUT,
        EventConfig::OUTPUT_LIST,
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
     * @var int[]|null
     */
    #[Assert\All(
        constraints: [
            new Assert\NotBlank(),
            new Assert\Type('integer'),
        ],
        groups: [
            TicketConfig::VALID,
        ],
    )]
    #[Groups([
        TicketConfig::INPUT,
        TicketConfig::OUTPUT,
        TicketConfig::OUTPUT_LIST,
    ])]
    public ?array $eventsList = null;

}
