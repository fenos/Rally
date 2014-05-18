<?php
/**
 * Created by Fabrizio Fenoglio.
 *
 * @package Rally v1.0.0
 * Released under MIT Licence
 *
 */

namespace Fenos\Rally\Models;


trait Relations {

    public function followers()
    {
        if (\Config::get('rally::polymorphic') === false)
        {
            return $this->hasMany('Fenos\Rally\Models\Follower','followed_id');
        }

        return $this->morphMany('Fenos\Rally\Models\Follower','follower');
    }

    public function followed()
    {
        if (\Config::get('rally::polymorphic') === false)
        {
            return $this->hasMany('Fenos\Rally\Models\Follower','follower_id');
        }

        return $this->morphMany('Fenos\Rally\Models\Follower','follower');
    }

}
