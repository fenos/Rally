<?php
/**
 * Created by Fabrizio Fenoglio.
 *
 * @package Rally v1.0.0
 * Released under MIT Licence
 *
 */

namespace Fenos\Rally\Repositories;

use Fenos\Rally\Models\Follower;
use Illuminate\Database\DatabaseManager;

/**
 * Class RallyRepository
 * @package Fenos\Rally\Repositories
 */
class RallyRepository implements RallyRepositoryInterface {

    /**
     * @var \Illuminate\Database\DatabaseManager
     */
    protected $db;

    /**
     * @var \Fenos\Rally\Models\Follower
     */
    private $follow;

    /**
     * @param Follower $follow
     * @param DatabaseManager $db
     */
    function __construct(Follower $follow,DatabaseManager $db)
    {
        $this->follow = $follow;
        $this->db = $db;
    }

    /**
     * @param array $follow_information
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function follow(array $follow_information)
    {
        return $this->follow->create($follow_information);
    }

    /**
     * @param $objectFollow
     * @return mixed
     */
    public function unFollow($objectFollow)
    {
        return $objectFollow->delete();
    }

    /**
     * @param array $follower
     * @return mixed
     */
    public function isFollower(array $follower)
    {
        return $this->follow->where('follower_id',  $follower['follower_id'])
            ->where('followed_id',$follower['followed_id'])
            ->first();

    }

    /**
     * @param array $followed
     * @param $filters
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function listsFollowers(array $followed,$filters)
    {
        $lists = $this->follow->with('follower')
            ->where('followed_id',$followed['follower_id']);

        $this->addFilters($lists,$filters);

        return $lists->get();
    }

    /**
     * @param array $followed
     * @param $filters
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function listsFollowing(array $followed,$filters)
    {
        $lists = $this->follow->with('follower')
            ->where('follower_id',$followed['follower_id']);

        $this->addFilters($lists,$filters);

        return $lists->get();
    }

    /**
     * @param array $followed
     * @return mixed
     */
    public function countFollowers(array $followed)
    {
        return $this->follow->select($this->db->raw('Count(*) as numbers_followers'))
            ->where('followed_id',$followed['follower_id'])->first();
    }

    /**
     * @param array $followed
     * @return mixed
     */
    public function countFollowing(array $followed)
    {
        return $this->follow->select($this->db->raw('Count(*) as numbers_followers'))
            ->where('follower_id',$followed['follower_id'])->first();
    }

    /**
     * Add filters to the selects queries
     *
     * @param $objectBuilder
     * @param array $filters
     */
    public function addFilters($objectBuilder,array $filters)
    {
        if (count($filters) > 0)
        {
            foreach($filters as $key => $filter)
            {
                if ($key == "orderBy")
                {
                    $field = 'created_at';
                    $objectBuilder->{$key}($field,$filter);
                }

                if ($key == "limit")
                {
                    $objectBuilder->{$key}($filter);
                }

                if ($key == "paginate")
                {
                    return $objectBuilder->{$key}($filter);
                }
            }
        }
    }

}
