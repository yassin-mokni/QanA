<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Question;

class Answer extends Model
{
    /**
     * Get the question that owns the Answer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
