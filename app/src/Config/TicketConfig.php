<?php

declare(strict_types=1);

namespace App\Config;

final class TicketConfig
{

    public const NAME_LENGTH        = 50;
    public const DESCRIPTION_LENGTH = 255;

    public const INPUT = 'TicketInput';

    public const OUTPUT      = 'TicketOutput';
    public const OUTPUT_LIST = 'TicketListOutput';

    public const OUTPUT_LIST_LIMIT  = 50;
    public const OUTPUT_LIST_OFFSET = 0;

    public const VALID        = 'TicketValid';
    public const VALID_CREATE = 'TicketValidCreate';
    public const VALID_UPDATE = 'TicketValidUpdate';

}
