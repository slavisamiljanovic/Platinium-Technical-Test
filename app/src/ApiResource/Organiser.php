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

use App\Config\OrganiserConfig;
use App\Controller\Api\OrganiserDeleteController;
use App\Dto\OrganiserDto;
use App\State\OrganiserCreateProcessor;
use App\State\OrganiserProvider;
use App\State\OrganisersProvider;
use App\State\OrganisersFeedProvider;
use App\State\OrganiserUpdateProcessor;

#[ApiResource(
    operations: [
        new Get(
            name: 'organiser_list',
            provider: OrganisersProvider::class,
            normalizationContext: [
                'groups' => [
                    OrganiserConfig::OUTPUT_LIST,
                ],
                'skip_null_values' => false,
            ],
            openapiContext: [
                'summary'     => 'List organisers.',
                'description' => 'List organisers.',
                'parameters'  => [
                    [
                        'name'        => 'limit',
                        'in'          => 'query',
                        'required'    => false,
                        'type'        => 'integer',
                        'schema'      => [
                            'type'    => 'integer',
                            'default' => OrganiserConfig::OUTPUT_LIST_LIMIT,
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
                            'default' => OrganiserConfig::OUTPUT_LIST_OFFSET,
                        ],                        
                        'description' => 'Starting point.',
                    ],
                ],
            ],
        ),
        new Get(
            name: 'organiser_feed',
            uriTemplate: '/organisers/feed',
            provider: OrganisersFeedProvider::class,
            normalizationContext: [
                'groups' => [
                    OrganiserConfig::OUTPUT_FEED_LIST,
                ],
                'skip_null_values' => false,
            ],
            openapiContext: [
                'summary'     => 'Organiser feed.',
                'description' => 'Organiser feed.<br>***The API will return ALL organisers.***',
            ],
        ),
        new Get(
            name: 'organiser_get',
            uriTemplate: '/organisers/{id}',
            uriVariables: [
                'id' => new Link(
                    fromClass: OrganiserDto::class,
                    fromProperty: 'id'
                ),
            ],
            provider: OrganiserProvider::class,
            normalizationContext: [
                'groups' => [
                    OrganiserConfig::OUTPUT,
                ],
                'skip_null_values' => false,
            ],
            openapiContext: [
                'summary'     => 'Single organiser data.',
                'description' => 'Single organiser data.',
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
            name: 'organiser_create',
            processor: OrganiserCreateProcessor::class,
            normalizationContext: [
                'groups' => [
                    OrganiserConfig::OUTPUT,
                ],
                'skip_null_values' => false,
            ],
            denormalizationContext: [
                'groups' => [
                    OrganiserConfig::INPUT,
                ],
            ],
            validationContext: [
                'groups' => [
                    OrganiserConfig::VALID,
                    OrganiserConfig::VALID_CREATE,
                    OrganiserConfig::VALID_UPDATE,
                ],
            ],
            openapiContext: [
                'summary'     => 'Create a new organiser.',
                'description' => 'Create a new organiser.',
            ],
        ),
        new Put(
            name: 'organiser_update',
            uriTemplate: '/organisers/{id}',
            uriVariables: [
                'id' => new Link(
                    fromClass: OrganiserDto::class,
                    fromProperty: 'id'
                ),
            ],
            read: false,
            processor: OrganiserUpdateProcessor::class,
            normalizationContext: [
                'groups' => [
                    OrganiserConfig::OUTPUT,
                ],
                'skip_null_values' => false,
            ],
            denormalizationContext: [
                'groups' => [
                    OrganiserConfig::INPUT,
                ],
            ],
            validationContext: [
                'groups' => [
                    OrganiserConfig::VALID,
                ],
            ],
            openapiContext: [
                'summary'     => 'Update an existing organiser.',
                'description' => 'Update an existing organiser.',
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
            name: 'organiser_delete',
            uriTemplate: '/organisers/{id}',
            uriVariables: [
                'id' => new Link(
                    fromClass: OrganiserDto::class,
                    fromProperty: 'id'
                ),
            ],
            controller: OrganiserDeleteController::class,
            read: false,
            openapiContext: [
                'summary'     => 'Delete an existing organiser.',
                'description' => 'Delete an existing organiser.',
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

final class Organiser
{
    /**
     * @var OrganiserDto[]
     */
    #[ApiProperty(
        builtinTypes: [
            new Type(
                builtinType: Type::BUILTIN_TYPE_ARRAY,
                collection: true,
                collectionValueType: [
                    new Type(
                        builtinType: Type::BUILTIN_TYPE_OBJECT,
                        class: OrganiserDto::class,
                    ),
                ],
            ),
        ],
    )]
    #[Groups([
        OrganiserConfig::OUTPUT_LIST,
        OrganiserConfig::OUTPUT_FEED_LIST,
    ])]
    public array $organisers = [];

    #[Groups([
        OrganiserConfig::OUTPUT_LIST,
        OrganiserConfig::OUTPUT_FEED_LIST,
    ])]
    public int $organisersCount = 0;

    #[Assert\Valid]
    #[Groups([
        OrganiserConfig::INPUT,
        OrganiserConfig::OUTPUT,
    ])]
    public ?OrganiserDto $organiser = null;
}
