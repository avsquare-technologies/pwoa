<?php

namespace App\Enums;

enum OrderStatus: string {
    case PENDING = 'pending';
    case RESERVED = 'reserved';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case PARTIAL = 'partial';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
}
