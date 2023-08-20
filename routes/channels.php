<?php

use App\Broadcasting\RowsChannel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('rows', RowsChannel::class);
