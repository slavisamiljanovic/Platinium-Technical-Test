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

use App\Config\TicketConfig;
use App\Controller\Api\TicketDeleteController;
use App\Dto\TicketDto;
use App\State\TicketCreateProcessor;
use App\State\TicketProvider;
use App\State\TicketsProvider;
use App\State\TicketUpdateProcessor;

#[ApiResource(
    operations: [
        new Get(
            name: 'ticket_list',
            provider: TicketsProvider::class,
            normalizationContext: [
                'groups' => [
                    TicketConfig::OUTPUT_LIST,
                ],
                'skip_null_values' => false,
            ],
            openapiContext: [
                'summary'     => 'List tickets.',
                'description' => 'List tickets.',
                'parameters'  => [
                    [
                        'name'        => 'limit',
                        'in'          => 'query',
                        'required'    => false,
                        'type'        => 'integer',
                        'schema'      => [
                            'type'    => 'integer',
                            'default' => TicketConfig::OUTPUT_LIST_LIMIT,
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
                            'default' => TicketConfig::OUTPUT_LIST_OFFSET,
                        ],                        
                        'description' => 'Starting point.',
                    ],
                ],
            ],
        ),
        new Get(
            name: 'ticket_get',
            uriTemplate: '/tickets/{id}',
            uriVariables: [
                'id' => new Link(
                    fromClass: TicketDto::class,
                    fromProperty: 'id'
                ),
            ],
            provider: TicketProvider::class,
            normalizationContext: [
                'groups' => [
                    TicketConfig::OUTPUT,
                ],
                'skip_null_values' => false,
            ],
            openapiContext: [
                'summary'     => 'Single ticket data.',
                'description' => 'Single ticket data.',
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
            name: 'ticket_create',
            processor: TicketCreateProcessor::class,
            normalizationContext: [
                'groups' => [
                    TicketConfig::OUTPUT,
                ],
                'skip_null_values' => false,
            ],
            denormalizationContext: [
                'groups' => [
                    TicketConfig::INPUT,
                ],
            ],
            validationContext: [
                'groups' => [
                    TicketConfig::VALID,
                    TicketConfig::VALID_CREATE,
                ],
            ],
            openapiContext: [
                'summary'     => 'Create a new ticket.',
                'description' => 'Create a new ticket.',
            ],
        ),
        new Put(
            name: 'ticket_update',
            uriTemplate: '/tickets/{id}',
            uriVariables: [
                'id' => new Link(
                    fromClass: TicketDto::class,
                    fromProperty: 'id'
                ),
            ],
            read: false,
            processor: TicketUpdateProcessor::class,
            normalizationContext: [
                'groups' => [
                    TicketConfig::OUTPUT,
                ],
                'skip_null_values' => false,
            ],
            denormalizationContext: [
                'groups' => [
                    TicketConfig::INPUT,
                ],
            ],
            validationContext: [
                'groups' => [
                    TicketConfig::VALID,
                    TicketConfig::VALID_UPDATE,
                ],
            ],
            openapiContext: [
                'summary'     => 'Update an existing ticket.',
                'description' => 'Update an existing ticket.',
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
            name: 'ticket_delete',
            uriTemplate: '/tickets/{id}',
            uriVariables: [
                'id' => new Link(
                    fromClass: TicketDto::class,
                    fromProperty: 'id'
                ),
            ],
            controller: TicketDeleteController::class,
            read: false,
            openapiContext: [
                'summary'     => 'Delete an existing ticket.',
                'description' => 'Delete an existing ticket.',
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

final class Ticket
{
    /**
     * @var TicketDto[]
     */
    #[ApiProperty(
        builtinTypes: [
            new Type(
                builtinType: Type::BUILTIN_TYPE_ARRAY,
                collection: true,
                collectionValueType: [
                    new Type(
                        builtinType: Type::BUILTIN_TYPE_OBJECT,
                        class: TicketDto::class,
                    ),
                ],
            ),
        ],
    )]
    #[Groups([
        TicketConfig::OUTPUT_LIST,
    ])]
    public array $tickets = [];

    #[Groups([
        TicketConfig::OUTPUT_LIST,
    ])]
    public int $ticketsCount = 0;

    #[Assert\Valid(
        groups: [
            TicketConfig::VALID,
            TicketConfig::VALID_UPDATE,
        ]
    )]
    #[Assert\NotNull(
        groups: [
            TicketConfig::VALID,
            TicketConfig::VALID_UPDATE,
        ]
    )]
    #[Groups([
        TicketConfig::INPUT,
        TicketConfig::OUTPUT,
    ])]
    public ?TicketDto $ticket = null;
}
