<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionDependency extends Model
{
    //
    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class, 'action_id');
    }

    public function depends_on_action_id(): BelongsTo
    {
        return $this->belongsTo(Action::class, 'action_id');
    }
}
