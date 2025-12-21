<?php

namespace App\Enums;

enum PledgeStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Refunded = 'refunded';
}
