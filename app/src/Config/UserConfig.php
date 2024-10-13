<?php

declare(strict_types=1);

namespace App\Config;

final class UserConfig
{
    public const EMAIL_LENGTH = 180;

    public const INPUT = 'UserInput';
    public const INPUT_CREATE = 'UserInputCreate';
    public const INPUT_LOGIN = 'UserInputLogin';
    public const INPUT_UPDATE = 'UserInputUpdate';

    public const OUTPUT = 'UserOutput';

    public const VALID = 'UserValid';
    public const VALID_CREATE = 'UserValidCreate';
    public const VALID_LOGIN = 'UserValidLogin';
    public const VALID_UPDATE = 'UserValidUpdate';
}
