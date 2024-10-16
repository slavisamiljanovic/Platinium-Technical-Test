<?php

declare(strict_types=1);

namespace App\Config;

final class EventConfig
{

    public const NAME_LENGTH        = 50;
    public const DESCRIPTION_LENGTH = 255;

    public const INPUT = 'EventInput';

    public const OUTPUT           = 'EventOutput';
    public const OUTPUT_LIST      = 'EventListOutput';
    public const OUTPUT_FEED_LIST = 'EventFeedListOutput';

    public const OUTPUT_LIST_LIMIT  = 10;
    public const OUTPUT_LIST_OFFSET = 0;

    public const VALID        = 'EventValid';
    public const VALID_CREATE = 'EventValidCreate';
    public const VALID_UPDATE = 'EventValidUpdate';

}
