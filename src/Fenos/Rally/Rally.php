<?php
/**
 * Created by Fabrizio Fenoglio.
 *
 * @package Rally v1.0.0
 * Released under MIT Licence
 *
 */

namespace Fenos\Rally;

use Fenos\Rally\Exceptions\AlreadyFollowerException;
use Fenos\Rally\Exceptions\FollowerNotFoundException;
use Fenos\Rally\Repositories\RallyRepositoryInterface;
use Illuminate\Config\Repository;


/**
 * Class Rally
 * @package Fenos\Rally
 */
class Rally {

    /**
     * @var Repositories\RallyRepository
     */
    private $rallyRepository;

    /**
     * @var array
     */
    protected $follower = [];
    /**
     * @var \Illuminate\Config\Repository
     */
    private $config;

    /**
     * @param RallyRepositoryInterface $rallyRepository
     * @param \Illuminate\Config\Repository $config
     */
    function __construct(RallyRepositoryInterface $rallyRepository, Repository $config)
    {
        $this->rallyRepository = $rallyRepository;
        $this->config = $config;
    }

    /**
     * Initialize the follower
     *
     * @param $follower_type
     * @param $follower_id
     * @return $this
     */
    public function follower($follower_type,$follower_id = false)
    {
        $this->lightValidation($follower_type, $follower_id);

        // store the informations on the property
        // if polymorphic is enabled it will store 2 paramets
        // others ways just 1 the ID
        if ($this->config->get('rally::polymorphic'))
        {
            $this->follower['follower_type'] = $follower_type;
            $this->follower['follower_id'] = $follower_id;
        }
        else
        {
            // I get the first parameter to be the id
            $this->follower['follower_id'] = $follower_type;
        }


        return $this;
    }

    /**
     * Follow a entity
     *
     * @param $followed_type
     * @param $followed_id
     * @return \Illuminate\Database\Eloquent\Model|static
     * @throws Exceptions\AlreadyFollowerException
     * @throws \InvalidArgumentException
     */
    public function follow($followed_type,$followed_id = false)
    {
        $this->lightValidation($followed_type, $followed_id);

        $isFollower = $this->isFollowerOf($followed_type,$followed_id);

        // before insert the relations between followers I need to check
        // if the follower is already fan of him.
        if ($isFollower !== false)
        {
            throw new AlreadyFollowerException('The follower is already followers of this Entity');
        }

        // check that there are the informations of the follower
        $this->checkFollowerInformation();

        if ($this->config->get('rally::polymorphic') !== false)
        {
            $this->follower['followed_type'] = $followed_type;
            $this->follower['followed_id'] = $followed_id;
        }
        else
        {
            $this->follower['followed_id'] = $followed_type;
        }


        $follow = $this->rallyRepository->follow($this->follower);

        return $follow;
    }

    /**
     * Check if is follower
     *
     * @param $followed_type
     * @param $followed_id
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function isFollowerOf($followed_type,$followed_id = false)
    {
        // check that there are the informations of the follower
        $this->checkFollowerInformation();

        if ($this->config->get('rally::polymorphic'))
        {
            $this->follower['followed_type'] = $followed_type;
            $this->follower['followed_id'] = $followed_id;
        }
        else
        {
            $this->follower['followed_id'] = $followed_type;
        }

        // check if the current entity is follower of the current entity
        $check = $this->rallyRepository->isFollower($this->follower);

        if (is_null($check))
        {
            return false;
        }

        return $check;
    }

    /**
     * Get list of following
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public function getListsFollowing(array $filters = [])
    {
        $this->checkFollowerInformation();

        return $this->rallyRepository->listsFollowing($this->follower,$filters);
    }

    /**
     * Unfollow Someone
     *
     * @param $followed_type
     * @param $followed_id
     * @return mixed
     * @throws Exceptions\FollowerNotFoundException
     */
    public function unFollow($followed_type,$followed_id = false)
    {
        $isFollower = $this->isFollowerOf($followed_type,$followed_id);

        if ($isFollower !== false)
        {
            return $this->rallyRepository->unFollow($isFollower);
        }

        throw new FollowerNotFoundException('Relation with thoose followers not found');
    }

    /**
     * Get lits of the followers
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @throws \InvalidArgumentException
     */
    public function getLists(array $filters = [])
    {
        $this->checkFollowerInformation();

        return $this->rallyRepository->listsFollowers($this->follower,$filters);
    }

    /**
     * Get only the number of followers
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function count()
    {
        // check that there are the informations of the follower
        $this->checkFollowerInformation();

        return $this->rallyRepository->countFollowers($this->follower);
    }

    /**
     * Count following
     *
     * @return mixed
     */
    public function countFollowing()
    {
        // check that there are the informations of the follower
        $this->checkFollowerInformation();

        return $this->rallyRepository->countFollowing($this->follower);
    }

    /**
     * Light validation for make sure that any kind of
     * value different to string or number will go on the databse
     * This not replace a normal validation
     *
     * @param $follower_type
     * @param $follower_id
     * @throws \InvalidArgumentException
     */
    public function lightValidation($follower_type, $follower_id = false)
    {
        // Check if the follower type is a string
        // and nothing else
        if ($this->config->get('rally::polymorphic'))
        {
            if (!is_string($follower_type)) {
                throw new \InvalidArgumentException('The follower type must be a string');
            }

            // Check if the id is a number and nothing else
            if (!is_numeric($follower_id)) {
                throw new \InvalidArgumentException('The follower Id must be a integer');
            }
        }
        else
        {
            // Check if the id is a number and nothing else
            if (!is_numeric($follower_type)) {
                throw new \InvalidArgumentException('The follower Id must be a integer');
            }
        }
    }

    /**
     * Check follower Information
     *
     * @throws \InvalidArgumentException
     */
    public function checkFollowerInformation()
    {
        // if the polymorphic option is active we must to check about
        // 2 partameters ID and TYPE if no Just 1 the ID,
        if ($this->config->get('rally::polymorphic') !== false)
        {
            // check that there are the informations of the follower
            if (count($this->follower) < 2) {
                throw new \InvalidArgumentException('You must specify the ID and type of the follower');
            }
        }
        else
        {
            // check that there are the informations of the follower
            if (count($this->follower) < 1) {
                throw new \InvalidArgumentException('You must specify the ID of the follower');
            }
        }

    }

    /**
     * @param $follower_type
     * @param $follower_id
     */
    public function setFollowerPolymorpich($follower_type,$follower_id)
    {
        $this->follower['follower_type'] = $follower_type;
        $this->follower['follower_id'] = $follower_id;
    }

    /**
     * @param $follower_id
     */
    public function setFollower($follower_id)
    {
        $this->follower['follower_id'] = $follower_id;
    }

}

// Rally::follower('Team',$team_id)->follow('User',$user_id);
// Rally::follower('Team',$team_id)->isFollowerOf('User',$user_id);
// Rally::follower('Team',$team_id)->unFollow('User',$user_id);
