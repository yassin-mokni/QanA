<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Answer;

class Question extends Model
{
    // /**
    //  * Get all of the answers for the Question
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\HasOne
    //  */
    // public function answer(): HasOne
    // {
    //     return $this->hasOne(Answer::class);
    // }


    /**
     * Get the answer associated with the Question
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function answer()
    {
        return $this->hasOne(answer::class);
    }
}
