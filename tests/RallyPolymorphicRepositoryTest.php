<?php
/**
 * Created by Fabrizio Fenoglio.
 *
 * @package Rally v1.0.0
 * Released under MIT Licence
 *
 */

use Fenos\Rally\Repositories\RallyPolymorphicRepository;
use Mockery as m;

class RallyPolymorphicRepositoryTest extends PHPUnit_Framework_TestCase {

    /**
     * @var $db
     */
    protected $db;
    /**
     * @var $follower
     */
    protected $follower;
    /**
     * @var $rallyRepo
     */
    protected $rallyRepo;

    /**
     * SetUp Unit test
     */
    public function setUp()
    {
        $this->mockEloquent = m::mock('Illuminate\Database\Eloquent\Model');

        $this->rallyRepo = new RallyPolymorphicRepository(
            $this->follower = m::mock('Fenos\Rally\Models\Follower'),
            $this->db = m::mock('Illuminate\Database\DatabaseManager')
        );
    }

    /**
     * TearDown
     */
    public function tearDown()
    {
        m::close();
    }

    public function test_is_follower()
    {
        $createFollowRelation = [

            'follower_type' => 'User',
            'follower_id'   => 1,
            'followed_type' => 'Team',
            'followed_id'   => 2
        ];

        $this->follower->shouldReceive('where')
            ->once()
            ->with('follower_type','User')
            ->andReturn($this->follower);

        $this->follower->shouldReceive('where')
            ->once()
            ->with('follower_id',1)
            ->andReturn($this->follower);

        // to reviewe test closure
        $this->follower->shouldReceive('where')
            ->once()
            ->andReturn($this->follower);

        $this->follower->shouldReceive('first')
            ->once()
            ->with()
            ->andReturn($this->follower);

        $this->rallyRepo->isFollower($createFollowRelation);
    }

    public function test_count_lists_of_followers()
    {
        $listsFollowRelation = [

            'follower_type' => 'User',
            'follower_id'   => 1,
        ];

        $mockRepo = m::mock('Fenos\Rally\Repositories\RallyPolymorphicRepository[addFilters]',[$this->follower,$this->db]);

        $this->follower->shouldReceive('with')
            ->once()
            ->with('follower')
            ->andReturn($this->follower);

        $this->follower->shouldReceive('where')
            ->with('followers.followed_type','User')
            ->once()
            ->andReturn($this->follower);

        $this->follower->shouldReceive('where')
            ->once()
            ->with('followers.followed_id',1)
            ->andReturn($this->follower);

        $this->follower->shouldReceive('leftJoin')
            ->once()
            ->with('followers as fol','followers.follower_id','=','fol.followed_id')
            ->andReturn($this->follower);

        $this->follower->shouldReceive('groupBy')
             ->once()
             ->with('followers.followed_id')
             ->andReturn($this->follower);

        $this->follower->shouldReceive('groupBy')
            ->once()
            ->with('fol.followed_id')
            ->andReturn($this->follower);

        $this->follower->shouldReceive('select')
             ->once()
             ->with('followers.*','fol.follower_id as fol_id')
             ->andReturn($this->follower);

        $mockRepo->shouldReceive('addFilters')
            ->once()
            ->with($this->follower,[])
            ->andReturn(null);

        $this->follower->shouldReceive('get')
            ->once()
            ->andReturn($this->follower);

        $result = $mockRepo->listsFollowers($listsFollowRelation,[]);

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

    public function test_count_lists_of_following()
    {
        $listsFollowRelation = [

            'follower_type' => 'User',
            'follower_id'   => 1,
        ];

        $mockRepo = m::mock('Fenos\Rally\Repositories\RallyPolymorphicRepository[addFilters]',[$this->follower,$this->db]);

        $this->follower->shouldReceive('with')
            ->once()
            ->with('follower')
            ->andReturn($this->follower);

        $this->follower->shouldReceive('where')
            ->with('follower_type','User')
            ->once()
            ->andReturn($this->follower);

        $this->follower->shouldReceive('where')
            ->once()
            ->with('follower_id',1)
            ->andReturn($this->follower);

        $mockRepo->shouldReceive('addFilters')
            ->once()
            ->with($this->follower,[])
            ->andReturn(null);

        $this->follower->shouldReceive('get')
            ->once()
            ->andReturn($this->follower);

        $result = $mockRepo->listsFollowing($listsFollowRelation,[]);

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

    public function test_count_followers()
    {
        $countFollowRelation = [

            'follower_type' => 'User',
            'follower_id'   => 1,
        ];

        $this->follower->shouldReceive('select')
            ->once()
            ->andReturn($this->follower);

        $this->db->shouldReceive('raw')
            ->once()
            ->with('Count(*) as numbers_followers')
            ->andReturn($this->follower);

        $this->follower->shouldReceive('where')
            ->once()
            ->with('followed_type','User')
            ->andReturn($this->follower);

        $this->follower->shouldReceive('where')
            ->once()
            ->with('followed_id',1)
            ->andReturn($this->follower);

        $this->follower->shouldReceive('first')
            ->once()
            ->andReturn($this->follower);

        $result = $this->rallyRepo->countFollowers($countFollowRelation);

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

    public function test_count_following()
    {
        $countFollowRelation = [

            'follower_type' => 'User',
            'follower_id'   => 1,
        ];

        $this->follower->shouldReceive('select')
            ->once()
            ->andReturn($this->follower);

        $this->db->shouldReceive('raw')
            ->once()
            ->with('Count(*) as numbers_followers')
            ->andReturn($this->follower);

        $this->follower->shouldReceive('where')
            ->once()
            ->with('follower_type','User')
            ->andReturn($this->follower);

        $this->follower->shouldReceive('where')
            ->once()
            ->with('follower_id',1)
            ->andReturn($this->follower);

        $this->follower->shouldReceive('first')
            ->once()
            ->andReturn($this->follower);

        $result = $this->rallyRepo->countFollowing($countFollowRelation);

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }
}

