<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

use App\Config\EventConfig;
use App\Config\TicketConfig;
use App\Config\DateTimeConfig;

final class EventDto
{

    #[ApiProperty(identifier: true)]

    #[Assert\NotBlank(
        groups: [
            TicketConfig::VALID_CREATE,
            TicketConfig::VALID_UPDATE,
        ]
    )]
    #[Assert\NotNull(
        groups: [
            TicketConfig::VALID_CREATE,
            TicketConfig::VALID_UPDATE,
        ]
    )]
    #[Assert\Type(
        'integer',
        groups: [
            TicketConfig::VALID_CREATE,
            TicketConfig::VALID_UPDATE,
        ]
    )]
    #[Groups([
        EventConfig::OUTPUT,
        EventConfig::OUTPUT_LIST,
        EventConfig::OUTPUT_FEED_LIST,
        TicketConfig::INPUT,
        TicketConfig::OUTPUT,
        TicketConfig::OUTPUT_LIST,
    ])]
    public ?int $id = null;

    #[Assert\Length(
        min: 1,
        max: EventConfig::NAME_LENGTH,
        groups: [
            EventConfig::VALID,
        ],
    )]
    #[Assert\NotBlank(
        groups: [
            EventConfig::VALID_CREATE,
            EventConfig::VALID_UPDATE,
            TicketConfig::VALID_CREATE,
            TicketConfig::VALID_UPDATE,
        ]
    )]
    #[Assert\NotNull(
        groups: [
            TicketConfig::VALID_CREATE,
            TicketConfig::VALID_UPDATE,
        ]
    )]
    #[Groups([
        EventConfig::INPUT,
        EventConfig::OUTPUT,
        EventConfig::OUTPUT_LIST,
        EventConfig::OUTPUT_FEED_LIST,
        TicketConfig::INPUT,
        TicketConfig::OUTPUT,
        TicketConfig::OUTPUT_LIST,
    ])]
    public ?string $name = null;

    #[Assert\Length(
        max: EventConfig::DESCRIPTION_LENGTH,
        groups: [
            EventConfig::VALID,
        ],
    )]
    #[Groups([
        EventConfig::INPUT,
        EventConfig::OUTPUT,
        EventConfig::OUTPUT_LIST,
    ])]
    public ?string $description = null;

    #[Assert\NotNull(
        groups: [
            EventConfig::VALID_CREATE,
            EventConfig::VALID_UPDATE,
        ]
    )]
    #[Assert\Type(
        type: 'bool',
        groups: [
            EventConfig::VALID_CREATE,
            EventConfig::VALID_UPDATE,
        ]
    )]    
    #[Groups([
        EventConfig::INPUT,
        EventConfig::OUTPUT,
        EventConfig::OUTPUT_LIST,
        EventConfig::OUTPUT_FEED_LIST,
        TicketConfig::OUTPUT,
    ])]
    public bool $isActive = true;

    #[Assert\Valid(
        groups: [
            EventConfig::VALID_CREATE,
            EventConfig::VALID_UPDATE,
        ]
    )]
    #[Assert\NotNull(
        groups: [
            EventConfig::VALID_CREATE,
            EventConfig::VALID_UPDATE,
        ]
    )]
    #[Groups([
        EventConfig::INPUT,
        EventConfig::OUTPUT,
        EventConfig::OUTPUT_LIST,
        TicketConfig::OUTPUT,
    ])]
    public ?OrganiserDto $organiser;

    #[Context([
        DateTimeNormalizer::FORMAT_KEY => DateTimeConfig::FORMAT,
    ])]
    #[Groups([
        EventConfig::OUTPUT,
        EventConfig::OUTPUT_LIST,
        TicketConfig::OUTPUT,
    ])]
    public DateTimeImmutable $createdAt;

    #[Context([
        DateTimeNormalizer::FORMAT_KEY => DateTimeConfig::FORMAT,
    ])]
    #[Groups([
        EventConfig::OUTPUT,
        EventConfig::OUTPUT_LIST,
        TicketConfig::OUTPUT,
    ])]
    public DateTimeImmutable $updatedAt;

    /*
    #[Groups([
        EventConfig::OUTPUT,
        EventConfig::OUTPUT_LIST,
    ])]
    public array $tickets = [];
    */

}
