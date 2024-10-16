<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use App\Config\EventConfig;
use App\Controller\Api\EventDeleteController;
use App\Dto\EventDto;
use App\State\EventCreateProcessor;
use App\State\EventProvider;
use App\State\EventsProvider;
use App\State\EventsFeedProvider;
use App\State\EventUpdateProcessor;

#[ApiResource(
    operations: [
        new Get(
            name: 'event_list',
            provider: EventsProvider::class,
            normalizationContext: [
                'groups' => [
                    EventConfig::OUTPUT_LIST,
                ],
                'skip_null_values' => false,
            ],
            openapiContext: [
                'summary'     => 'List events.',
                'description' => 'List events.',
                'parameters'  => [
                    [
                        'name'        => 'limit',
                        'in'          => 'query',
                        'required'    => false,
                        'type'        => 'integer',
                        'schema'      => [
                            'type'    => 'integer',
                            'default' => EventConfig::OUTPUT_LIST_LIMIT,
                        ],                        
                        'description' => 'Maximum number of results.',
                    ],
                    [
                        'name'        => 'offset',
                        'in'          => 'query',
                        'required'    => false,
                        'type'        => 'integer',
                        'schema'      => [
                            'type'    => 'integer',
                            'default' => EventConfig::OUTPUT_LIST_OFFSET,
                        ],                        
                        'description' => 'Starting point.',
                    ],
                ],
            ],
        ),
        new Get(
            name: 'event_feed',
            uriTemplate: '/events/feed',
            provider: EventsFeedProvider::class,
            normalizationContext: [
                'groups' => [
                    EventConfig::OUTPUT_FEED_LIST,
                ],
                'skip_null_values' => false,
            ],
            openapiContext: [
                'summary'     => 'Events feed.',
                'description' => 'Events feed.<br>***The API will return ALL events.***',
            ],
        ),
        new Get(
            name: 'event_get',
            uriTemplate: '/events/{id}',
            uriVariables: [
                'id' => new Link(
                    fromClass: EventDto::class,
                    fromProperty: 'id'
                ),
            ],
            provider: EventProvider::class,
            normalizationContext: [
                'groups' => [
                    EventConfig::OUTPUT,
                ],
                'skip_null_values' => false,
            ],
            openapiContext: [
                'summary'     => 'Single event data.',
                'description' => 'Single event data.',
                'parameters'  => [
                    [
                        'name'     => 'id',
                        'in'       => 'path',
                        'required' => true,
                        'type'     => 'integer',
                        'schema'   => [
                            'type' => 'integer',
                        ],                        
                    ],
                ],
            ],
        ),
        new Post(
            name: 'event_create',
            processor: EventCreateProcessor::class,
            normalizationContext: [
                'groups' => [
                    EventConfig::OUTPUT,
                ],
                'skip_null_values' => false,
            ],
            denormalizationContext: [
                'groups' => [
                    EventConfig::INPUT,
                ],
            ],
            validationContext: [
                'groups' => [
                    EventConfig::VALID_CREATE,
                    EventConfig::VALID,
                ],
            ],
            openapiContext: [
                'summary'     => 'Create a new event.',
                'description' => 'Create a new event.',
            ],
        ),
        new Put(
            name: 'event_update',
            uriTemplate: '/events/{id}',
            uriVariables: [
                'id' => new Link(
                    fromClass: EventDto::class,
                    fromProperty: 'id'
                ),
            ],
            read: false,
            processor: EventUpdateProcessor::class,
            normalizationContext: [
                'groups' => [
                    EventConfig::OUTPUT,
                ],
                'skip_null_values' => false,
            ],
            denormalizationContext: [
                'groups' => [
                    EventConfig::INPUT,
                ],
            ],
            validationContext: [
                'groups' => [
                    EventConfig::VALID_UPDATE,
                    EventConfig::VALID,
                ],
            ],
            openapiContext: [
                'summary'     => 'Update an existing event.',
                'description' => 'Update an existing event.',
                'parameters'  => [
                    [
                        'name'     => 'id',
                        'in'       => 'path',
                        'required' => true,
                        'type'     => 'integer',
                        'schema'   => [
                            'type' => 'integer',
                        ],                        
                    ],
                ],
            ],
        ),
        new Delete(
            name: 'event_delete',
            uriTemplate: '/events/{id}',
            uriVariables: [
                'id' => new Link(
                    fromClass: EventDto::class,
                    fromProperty: 'id'
                ),
            ],
            controller: EventDeleteController::class,
            read: false,
            openapiContext: [
                'summary'     => 'Delete an existing event.',
                'description' => 'Delete an existing event.',
                'parameters'  => [
                    [
                        'name'     => 'id',
                        'in'       => 'path',
                        'required' => true,
                        'type'     => 'integer',
                        'schema'   => [
                            'type' => 'integer',
                        ],                        
                    ],
                ],
            ],
        ),
    ],
)]

final class Event
{
    /**
     * @var EventDto[]
     */
    #[ApiProperty(
        builtinTypes: [
            new Type(
                builtinType: Type::BUILTIN_TYPE_ARRAY,
                collection: true,
                collectionValueType: [
                    new Type(
                        builtinType: Type::BUILTIN_TYPE_OBJECT,
                        class: EventDto::class,
                    ),
                ],
            ),
        ],
    )]
    #[Groups([
        EventConfig::OUTPUT_LIST,
        EventConfig::OUTPUT_FEED_LIST,
    ])]
    public array $events = [];

    #[Groups([
        EventConfig::OUTPUT_LIST,
        EventConfig::OUTPUT_FEED_LIST,
    ])]
    public int $eventsCount = 0;

    #[Assert\Valid]
    #[Groups([
        EventConfig::INPUT,
        EventConfig::OUTPUT,
    ])]
    public ?EventDto $event = null;
}
