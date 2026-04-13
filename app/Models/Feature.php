<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Feature extends Model
{
    protected $fillable = ['key', 'label', 'role', 'is_enabled', 'mode', 'starts_at', 'ends_at'];

    protected $casts = [
        'is_enabled' => 'boolean',
        'starts_at'  => 'datetime',
        'ends_at'    => 'datetime',
    ];

    public function isCurrentlyEnabled(): bool
    {
        $now = Carbon::now();

        return match($this->mode) {
            'permanent'     => $this->is_enabled,
            'scheduled_off' => !($this->starts_at && $this->ends_at && $now->between($this->starts_at, $this->ends_at)),
            'scheduled_on'  => $this->starts_at && $this->ends_at && $now->between($this->starts_at, $this->ends_at),
            default         => true,
        };
    }
}
