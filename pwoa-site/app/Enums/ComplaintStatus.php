<?php

namespace App\Enums;

enum ComplaintStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public function getLabel(): string
    {
        return match($this) {
            self::OPEN => 'Open',
            self::IN_PROGRESS => 'In Progress',
            self::RESOLVED => 'Resolved',
            self::CLOSED => 'Closed',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::OPEN => 'gray',
            self::IN_PROGRESS => 'warning',
            self::RESOLVED => 'success',
            self::CLOSED => 'danger',
        };
    }
}
