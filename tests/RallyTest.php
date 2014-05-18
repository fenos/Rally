<?php
/**
 * Created by Fabrizio Fenoglio.
 *
 * @package Rally v1.0.0
 * Released under MIT Licence
 *
 */

use Fenos\Rally\Rally;
use Mockery as m;

/**
 * Class RallyTest
 */
class RallyTest extends PHPUnit_Framework_TestCase {

    /**
     * @var
     */
    protected $rally;
    /**
     * @var
     */
    protected $config;
    /**
     * @var
     */
    protected $rallyRepo;

    /**
     * SetUp Unit Test
     */
    public function setUp()
    {
        $this->rally = new Rally(
            $this->rallyRepo = m::mock('Fenos\Rally\Repositories\RallyRepositoryInterface'),
            $this->config = m::mock('Illuminate\Config\Repository')
        );

        $this->mockEloquent = m::mock('Illuminate\Database\Eloquent\Model');
    }

    public function tearDown()
    {
        m::close();
    }

    public function test_follower_selector_polymorphic()
    {
        $mockRally = m::mock('Fenos\Rally\Rally[lightValidation]',[$this->rallyRepo,$this->config]);

        $mockRally->shouldReceive('lightValidation')
            ->once()
            ->with('User',1)
            ->andReturn(null);

        $this->config->shouldReceive('get')
            ->once()
            ->with('rally::polymorphic')
            ->andReturn(true);

        $result = $mockRally->follower('User',1);

        $this->assertInstanceOf('Fenos\Rally\Rally',$result);
    }

    public function test_follower_selector_NOT_polymorphic()
    {
        $mockRally = m::mock('Fenos\Rally\Rally[lightValidation]',[$this->rallyRepo,$this->config]);

        $mockRally->shouldReceive('lightValidation')
            ->once()
            ->with(1,false)
            ->andReturn(null);

        $this->config->shouldReceive('get')
            ->once()
            ->with('rally::polymorphic')
            ->andReturn(false);

        $result = $mockRally->follower(1);

        $this->assertInstanceOf('Fenos\Rally\Rally',$result);
    }

    public function test_follow_someone_polymorphic()
    {
        $createFollowRelation = [

            'follower_type' => 'User',
            'follower_id'   => 1,
            'followed_type' => 'Team',
            'followed_id'   => 2
        ];

        $mockRally = m::mock('Fenos\Rally\Rally[lightValidation,isFollowerOf,checkFollowerInformation]',[$this->rallyRepo,$this->config]);

        $mockRally->setFollowerPolymorpich('User',1);

        $mockRally->shouldReceive('lightValidation')
            ->once()
            ->with('Team',2)
            ->andReturn(null);

        $mockRally->shouldReceive('isFollowerOf')
            ->once()
            ->with('Team',2)
            ->andReturn(false);

        $mockRally->shouldReceive('checkFollowerInformation')
            ->once()
            ->andReturn(null);

        $this->config->shouldReceive('get')
            ->once()
            ->with('rally::polymorphic')
            ->andReturn(true);

        $this->rallyRepo->shouldReceive('follow')
            ->once()
            ->with($createFollowRelation)
            ->andReturn(m::mock('Fenos\Rally\Models\Follower'));

        $result = $mockRally->follow('Team',2);

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

    public function test_follow_someone_NOT_polymorphic()
    {
        $createFollowRelation = [

            'follower_id'   => 1,
            'followed_id'   => 2
        ];

        $mockRally = m::mock('Fenos\Rally\Rally[lightValidation,isFollowerOf,checkFollowerInformation]',[$this->rallyRepo,$this->config]);

        $mockRally->setFollower(1);

        $mockRally->shouldReceive('lightValidation')
            ->once()
            ->with(2,false)
            ->andReturn(null);

        $mockRally->shouldReceive('isFollowerOf')
            ->once()
            ->with(2,false)
            ->andReturn(false);

        $mockRally->shouldReceive('checkFollowerInformation')
            ->once()
            ->andReturn(null);

        $this->config->shouldReceive('get')
            ->once()
            ->with('rally::polymorphic')
            ->andReturn(false);

        $this->rallyRepo->shouldReceive('follow')
            ->once()
            ->with($createFollowRelation)
            ->andReturn(m::mock('Fenos\Rally\Models\Follower'));

        $result = $mockRally->follow(2,false);

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

    public function test_the_check_if_is_follower_of_someone_polymorphic()
    {
        $checkFollowRelation = [

            'follower_type' => 'User',
            'follower_id'   => 1,
            'followed_type' => 'Team',
            'followed_id'   => 2
        ];

        $mockRally = m::mock('Fenos\Rally\Rally[checkFollowerInformation]',[$this->rallyRepo,$this->config]);

        $mockRally->setFollowerPolymorpich('User',1);

        $mockRally->shouldReceive('checkFollowerInformation')
            ->once()
            ->andReturn(null);

        $this->config->shouldReceive('get')
            ->once()
            ->with('rally::polymorphic')
            ->andReturn(true);

        $this->rallyRepo->shouldReceive('isFollower')
            ->once()
            ->with($checkFollowRelation)
            ->andReturn(m::mock('Fenos\Rally\Models\Follower'));

        $result = $mockRally->isFollowerOf('Team',2);

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

    public function test_the_check_if_is_follower_of_someone_NOT_polymorphic()
    {
        $checkFollowRelation = [

            'follower_id'   => 1,
            'followed_id'   => 2
        ];

        $mockRally = m::mock('Fenos\Rally\Rally[checkFollowerInformation]',[$this->rallyRepo,$this->config]);

        $mockRally->setFollower(1);

        $mockRally->shouldReceive('checkFollowerInformation')
            ->once()
            ->andReturn(null);

        $this->config->shouldReceive('get')
            ->once()
            ->with('rally::polymorphic')
            ->andReturn(false);

        $this->rallyRepo->shouldReceive('isFollower')
            ->once()
            ->with($checkFollowRelation)
            ->andReturn(m::mock('Fenos\Rally\Models\Follower'));

        $result = $mockRally->isFollowerOf(2);

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

    public function test_the_check_if_is_follower_of_someone__polymorphic_on_not_found()
    {
        $checkFollowRelation = [

            'follower_id'   => 1,
            'followed_id'   => 2
        ];

        $mockRally = m::mock('Fenos\Rally\Rally[checkFollowerInformation]',[$this->rallyRepo,$this->config]);

        $mockRally->setFollower(1);

        $mockRally->shouldReceive('checkFollowerInformation')
            ->once()
            ->andReturn(null);

        $this->config->shouldReceive('get')
            ->once()
            ->with('rally::polymorphic')
            ->andReturn(false);

        $this->rallyRepo->shouldReceive('isFollower')
            ->once()
            ->with($checkFollowRelation)
            ->andReturn(null);

        $result = $mockRally->isFollowerOf(2);

        $this->assertFalse($result);
    }

    public function test_unfollow_someone()
    {
        $mockRally = m::mock('Fenos\Rally\Rally[isFollowerOf]',[$this->rallyRepo,$this->config]);

        $mockRally->setFollowerPolymorpich('User',1);

        $mockRally->shouldReceive('isFollowerOf')
            ->once()
            ->with('Team',2)
            ->andReturn(m::mock('Fenos\Rally\Models\Follower'));

        // test to review
        $this->rallyRepo->shouldReceive('unFollow')
            ->once()
            ->andReturn(true);

        $result = $mockRally->unFollow('Team',2);

        $this->assertTrue($result);
    }

    /**
     * @expectedException Fenos\Rally\Exceptions\FollowerNotFoundException
     * */
    public function test_unfollow_someone_but_relation_not_found()
    {
        $mockRally = m::mock('Fenos\Rally\Rally[isFollowerOf]',[$this->rallyRepo,$this->config]);

        $mockRally->setFollowerPolymorpich('User',1);

        $mockRally->shouldReceive('isFollowerOf')
            ->once()
            ->with('Team',2)
            ->andReturn(false);

        $mockRally->unFollow('Team',2);

    }

    public function test_get_lists_followers_polymorphic()
    {
        $followerInfo = [
          'follower_type' => 'User',
          'follower_id'   => 1
        ];

        $mockRally = m::mock('Fenos\Rally\Rally[checkFollowerInformation]',[$this->rallyRepo,$this->config]);

        $mockRally->setFollowerPolymorpich('User',1);

        $mockRally->shouldReceive('checkFollowerInformation')
            ->once()
            ->andReturn(null);

        $this->rallyRepo->shouldReceive('listsFollowers')
            ->once()
            ->with($followerInfo,[])
            ->andReturn(m::mock('Fenos\Rally\Models\Follower'));

        $result = $mockRally->getLists();

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

    public function test_get_lists_followers_NOT_polymorphic()
    {
        $followerInfo = [
            'follower_id'   => 1
        ];

        $mockRally = m::mock('Fenos\Rally\Rally[checkFollowerInformation]',[$this->rallyRepo,$this->config]);

        $mockRally->setFollower(1);

        $mockRally->shouldReceive('checkFollowerInformation')
            ->once()
            ->andReturn(null);

        $this->rallyRepo->shouldReceive('listsFollowers')
            ->once()
            ->with($followerInfo,[])
            ->andReturn(m::mock('Fenos\Rally\Models\Follower'));

        $result = $mockRally->getLists();

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

    public function test_get_numbers_followers_polymorphic()
    {
        $followerInfo = [
            'follower_type' => 'User',
            'follower_id'   => 1
        ];

        $mockRally = m::mock('Fenos\Rally\Rally[checkFollowerInformation]',[$this->rallyRepo,$this->config]);

        $mockRally->setFollowerPolymorpich('User',1);

        $mockRally->shouldReceive('checkFollowerInformation')
            ->once()
            ->andReturn(null);

        $this->rallyRepo->shouldReceive('countFollowers')
            ->once()
            ->with($followerInfo)
            ->andReturn(m::mock('Fenos\Rally\Models\Follower'));

        $result = $mockRally->count();

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

    public function test_get_numbers_followers_NOT_polymorphic()
    {
        $followerInfo = [
            'follower_id'   => 1
        ];

        $mockRally = m::mock('Fenos\Rally\Rally[checkFollowerInformation]',[$this->rallyRepo,$this->config]);

        $mockRally->setFollower(1);

        $mockRally->shouldReceive('checkFollowerInformation')
            ->once()
            ->andReturn(null);

        $this->rallyRepo->shouldReceive('countFollowers')
            ->once()
            ->with($followerInfo)
            ->andReturn(m::mock('Fenos\Rally\Models\Follower'));

        $result = $mockRally->count();

        $this->assertInstanceOf('Fenos\Rally\Models\Follower',$result);
    }

    public function test_lightValidation_for_polymorphic()
    {
        $this->config->shouldReceive('get')
            ->once()
            ->with('rally::polymorphic')
            ->andReturn(true);

        $result = $this->rally->lightValidation('User',1);

        $this->assertNull($result);
    }

    /**
     * @expectedException \InvalidArgumentException
     * */
    public function test_lightValidation_for_polymorphic_having_first_parameter_not_string()
    {
        $this->config->shouldReceive('get')
            ->once()
            ->with('rally::polymorphic')
            ->andReturn(true);

        $this->rally->lightValidation(10,1);

    }

    /**
     * @expectedException \InvalidArgumentException
     * */
    public function test_lightValidation_for_polymorphic_having_second_parameter_not_number()
    {
        $this->config->shouldReceive('get')
            ->once()
            ->with('rally::polymorphic')
            ->andReturn(true);

        $this->rally->lightValidation('must be','a number');
    }

    public function test_lightValidation_for_NOT_polymorphic()
    {
        $this->config->shouldReceive('get')
            ->once()
            ->with('rally::polymorphic')
            ->andReturn(false);

        $result = $this->rally->lightValidation(1);

        $this->assertNull($result);
    }

    public function test_check_follower_information_exists_polymorphic()
    {
        $this->rally->setFollowerPolymorpich('Team',1);

        $this->config->shouldReceive('get')
            ->once()
            ->with('rally::polymorphic')
            ->andReturn(true);

        $result = $this->rally->checkFollowerInformation();

        $this->assertNull($result);
    }

    /**
     * @expectedException \InvalidArgumentException
     * */
    public function test_check_follower_information_exists_polymorphic_not_setting_both_values()
    {
        $this->config->shouldReceive('get')
            ->once()
            ->with('rally::polymorphic')
            ->andReturn(true);

        $this->rally->checkFollowerInformation();
    }

    public function test_check_follower_information_exists_NOT_polymorphic()
    {
        $this->rally->setFollower(1);

        $this->config->shouldReceive('get')
            ->once()
            ->with('rally::polymorphic')
            ->andReturn(false);

        $result = $this->rally->checkFollowerInformation();

        $this->assertNull($result);
    }
}

