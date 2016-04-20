<?php
/**
 * Created by Fabrizio Fenoglio.
 *
 * @package Rally v1.0.0
 * Released under MIT Licence
 *
 */

namespace Fenos\Rally\Models;


use Illuminate\Database\Eloquent\Model;

class Follower extends Model {

    protected $table = "followers";
    protected $fillable = ['follower_id','follower_type','followed_id','followed_type'];

    public function follower()
    {
        if (\Config::get('messenger.polymorphic') !== false)
        {
            return $this->morphTo();
        }

        return $this->belongsTo(\Config::get('messenger.model'),'follower_id');
    }

    public function followed()
    {
        if (\Config::get('messenger.polymorphic') !== false)
        {
            return $this->morphTo();
        }

        return $this->hasOne(\Config::get('messenger.model'),'follower_id');
    }

}
