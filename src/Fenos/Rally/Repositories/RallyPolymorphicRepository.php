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
 * Class RallyPolymorphicRepository
 * @package Fenos\Rally\Repositories
 */
class RallyPolymorphicRepository extends RallyRepository implements RallyRepositoryInterface {

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

        parent::__construct($this->follow,$this->db);
    }

    /**
     * @param array $follower
     * @return mixed
     */
    public function isFollower(array $follower)
    {
        return $this->follow->where('follower_type',$follower['follower_type'])
                            ->where('follower_id',  $follower['follower_id'])
                            ->where(function($where) use ($follower){
                                $where->where('followed_type',$follower['followed_type'])
                                ->where('followed_id',$follower['followed_id']);
                            })->first();

    }

    /**
     * @param array $followed
     * @param $filters
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function listsFollowers(array $followed,$filters)
    {
        $lists = $this->follow->with('follower')
                              ->where('followers.followed_type', $followed['follower_type'])
                              ->where('followers.followed_id',$followed['follower_id'])
                              ->leftJoin('followers as fol',function($join) use ($followed)
                                {
                                    $join->on('fol.follower_id','=','followers.followed_id')
                                        ->on('fol.followed_id','=','followers.follower_id');
                                })
                              ->groupBy('followers.followed_id')
                              ->groupBy('fol.followed_id')
                              ->select('followers.*',"fol.followed_id as is_fan");

        $this->addFilters($lists,$filters);

        if (array_key_exists('paginate',$filters))
        {
            return $lists->paginate($filters['paginate']);
        }

        return $lists->get();
    }

    /**
     * @param       $entity_id
     * @param       $entity_type
     * @param array $followed
     * @param       $filters
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\Paginator|static[]
     */
    public function listsWithImFollow($entity_id,$entity_type,array $followed,$filters)
    {
        $lists = $this->follow->with('follower')
            ->where('followed_type', $followed['follower_type'])
            ->where('followed_id',$followed['follower_id']);

        $this->addFilters($lists,$filters);

        if (array_key_exists('paginate',$filters))
        {
            return $lists->paginate($filters['paginate']);
        }

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
            ->where('follower_type', $followed['follower_type'])
            ->where('follower_id',$followed['follower_id']);

        $this->addFilters($lists,$filters);

        if (array_key_exists('paginate',$filters))
        {
            return $lists->paginate($filters['paginate']);
        }

        return $lists->get();
    }

    /**
     * @param array $followed
     * @return mixed
     */
    public function countFollowers(array $followed)
    {
        return $this->follow->select($this->db->raw('Count(*) as numbers_followers'))
            ->where('followed_type', $followed['follower_type'])
            ->where('followed_id',$followed['follower_id'])->first();
    }

    /**
     * @param array $followed
     * @return mixed
     */
    public function countFollowing(array $followed)
    {
        return $this->follow->select($this->db->raw('Count(*) as numbers_followers'))
            ->where('follower_type', $followed['follower_type'])
            ->where('follower_id',$followed['follower_id'])->first();
    }

    /**
     * @param       $followed
     * @param array $filters
     * @return mixed
     */
    public function emptyQuery($followed,array $filters)
    {
        $lists = $this->follow->with('follower')
            ->where('followers.followed_type', $followed['follower_type'])
            ->where('followers.followed_id',$followed['follower_id']);

        $this->addFilters($lists,$filters);

        return $lists;
    }
}
