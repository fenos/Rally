<?php
/**
 * Created by Fabrizio Fenoglio.
 *
 * @package Rally v1.0.0
 * Released under MIT Licence
 *
 */

use Mockery as m;

/**
 * Class RallyRepositoryTest
 */
class RallyRepositoryTest extends PHPUnit_Framework_TestCase {

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

        $this->rallyRepo = new \Fenos\Rally\Repositories\RallyRepository(
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

    public function test_follow_someone()
    {
        $createFollowRelation = [

            'follower_id'   => 1,
            'followed_id'   => 2
        ];

        $this->follower->shouldReceive('create')
            ->with($createFollowRelation)
            ->once()
            ->andReturn($this->follower);

        $result = $this->rallyRepo->follow($createFollowRelation);

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

    public function test_Unfollow_someone()
    {

        $this->follower->shouldReceive('delete')
            ->with()
            ->once()
            ->andReturn(true);

        $result = $this->rallyRepo->unfollow($this->follower);

        $this->assertTrue($result);
    }

    public function test_if_someOne_is_follower_of_somebody()
    {
        $this->follower->shouldReceive('where')
            ->once()
            ->with('follower_id',1)
            ->andReturn($this->follower);

        $this->follower->shouldReceive('where')
            ->once()
            ->with('followed_id',2)
            ->andReturn($this->follower);

        $this->follower->shouldReceive('first')
            ->once()
            ->with()
            ->andReturn($this->follower);

        $result = $this->rallyRepo->isFollower(['follower_id' =>1,'followed_id' => 2]);

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

    public function test_listsFollowers()
    {
        $createFollowRelation = [

            'follower_id'   => 1
        ];

        $mockRepo = m::mock('Fenos\Rally\Repositories\RallyRepository[addFlters]',[$this->follower,$this->db]);

        $this->follower->shouldReceive('with')
            ->once()
            ->with('follower')
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
            ->with('fol.follower_id')
            ->andReturn($this->follower);

        $this->follower->shouldReceive('select')
            ->once()
            ->with('followers.*','fol.follower_id as fol_id')
            ->andReturn($this->follower);

        $mockRepo->shouldReceive('addFilters')
            ->with($this->follower,[])
            ->andReturn(null);

        $this->follower->shouldReceive('get')
            ->once()
            ->andReturn($this->follower);

        $result = $mockRepo->listsFollowers($createFollowRelation,[]);

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);

    }

    public function test_listsFollowing()
    {
        $createFollowRelation = [

            'follower_id'   => 1
        ];

        $mockRepo = m::mock('Fenos\Rally\Repositories\RallyRepository[addFlters]',[$this->follower,$this->db]);

        $this->follower->shouldReceive('with')
            ->once()
            ->with('follower')
            ->andReturn($this->follower);

        $this->follower->shouldReceive('where')
            ->once()
            ->with('follower_id',1)
            ->andReturn($this->follower);

        $mockRepo->shouldReceive('addFilters')
            ->with($this->follower,[])
            ->andReturn(null);

        $this->follower->shouldReceive('get')
            ->once()
            ->andReturn($this->follower);

        $result = $mockRepo->listsFollowing($createFollowRelation,[]);

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

    public function test_count_followers()
    {
        $createFollowRelation = [

            'follower_id'   => 1
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
            ->with('followed_id',1)
            ->andReturn($this->follower);

        $this->follower->shouldReceive('first')
            ->once()
            ->andReturn($this->follower);

        $result = $this->rallyRepo->countFollowers($createFollowRelation);

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

    public function test_count_following()
    {
        $createFollowRelation = [

            'follower_id'   => 1
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
            ->with('follower_id',1)
            ->andReturn($this->follower);

        $this->follower->shouldReceive('first')
            ->once()
            ->andReturn($this->follower);

        $result = $this->rallyRepo->countFollowing($createFollowRelation);

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

}

