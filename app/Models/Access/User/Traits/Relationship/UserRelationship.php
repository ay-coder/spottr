<?php

namespace App\Models\Access\User\Traits\Relationship;

use App\Models\Event\Event;
use App\Models\System\Session;
use App\Models\Access\User\SocialLogin;
use App\Models\Connections\Connections;
use App\Models\Posts\Posts;
use App\Models\Notifications\Notifications;

/**
 * Class UserRelationship.
 */
trait UserRelationship
{
    /**
     * Many-to-Many relations with Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('access.role'), config('access.role_user_table'), 'user_id', 'role_id');
    }

    /**
     * @return mixed
     */
    public function providers()
    {
        return $this->hasMany(SocialLogin::class);
    }

    /**
     * @return mixed
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    /**
     * @return mixed
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * @return mixed
     */
    public function connections()
    {
        return $this->hasMany(Connections::class, 'user_id');
    } 

    /**
     * @return mixed
     */
    public function posts()
    {
        return $this->hasMany(Posts::class, 'user_id');
    } 

    /**
     * @return mixed
     */
    public function user_notifications()
    {
        return $this->hasMany(Notifications::class, 'user_id');
    }    
}
