<?php namespace App\Models\Blocked\Traits\Relationship;

use App\Models\Posts\Posts;
use App\Models\Access\User\User;

trait Relationship
{
	/**
     * Belongs to relations with User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }	

    /**
     * Belongs to relations with User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function post()
    {
        return $this->belongsTo(Posts::class, 'post_id');
    }	
}