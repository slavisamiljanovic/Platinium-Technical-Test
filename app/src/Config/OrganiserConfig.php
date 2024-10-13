<?php

declare(strict_types=1);

namespace App\Config;

final class OrganiserConfig
{

    public const NAME_LENGTH        = 50;
    public const CITY_LENGTH        = 20;
    public const PHONE_LENGTH       = 20;
    public const DESCRIPTION_LENGTH = 255;

    public const INPUT = 'OrganiserInput';

    public const OUTPUT      = 'OrganiserOutput';
    public const OUTPUT_LIST = 'OrganiserListOutput';

    public const OUTPUT_LIST_LIMIT  = 50;
    public const OUTPUT_LIST_OFFSET = 0;

    public const VALID        = 'OrganiserValid';
    public const VALID_CREATE = 'OrganiserValidCreate';
    public const VALID_UPDATE = 'OrganiserValidUpdate';

}
