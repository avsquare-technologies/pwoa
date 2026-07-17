<?php

namespace App\Enums;

enum TransferStatus: string {
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
}
