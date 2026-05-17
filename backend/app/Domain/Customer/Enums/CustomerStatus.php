<?php

namespace App\Domain\Customer\Enums;

enum CustomerStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Prospect = 'prospect';
    case Churned = 'churned';
}
