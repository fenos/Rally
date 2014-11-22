Rally
=====

[![Build Status](https://travis-ci.org/fenos/Rally.svg?branch=master)](https://travis-ci.org/fenos/Rally)
[![ProjectStatus](http://stillmaintained.com/fenos/Rally.png)](http://stillmaintained.com/fenos/Rally)
[![Latest Stable Version](https://poser.pugx.org/fenos/rally/v/stable.png)](https://packagist.org/packages/fenos/rally)
[![License](https://poser.pugx.org/fenos/rally/license.png)](https://packagist.org/packages/fenos/rally)

Follow, Let Follow you, Follow with Rally. Rally is a plugin that implement in your application the follow system. It is quick to implement on your laravel project.
It give you the freedom to create your own followers system. It is can be polymorphic, so you can follow anybody or anything you want. The package has been released for laravel 4.*

* [Installation](#installation)
* [Documentation](#documentation)
    * [Follow](#follow)
    * [unFollow](#unfollow)
    * [Check if Is follow of](#check-if-is-follow-of)
    * [Get Lists of followers](#get-lists-of-followers)
    * [Count Followers](#count-followers)
    * [Note](#note)
    * [Tests](#tests)
    * [Credits](#credits)


## Installation ##

### Step 1 ###

Add it on your composer.json

~~~
"fenos/rally": "1.0.*"
~~~

and run **composer update**


### Step 2 ###

Add the following string to **app/config/app.php**

**Providers array:**

~~~
'Fenos\Rally\RallyServiceProvider'
~~~

**Aliases array:**

~~~
'Rally'    => 'Fenos\Rally\Facades\Rally'
~~~

### Step 3 ###

#### Migration ####

Make sure that your settings on **app/config/database.php** are correct, then make the migration typing:

~~~
php artisan migrate --package="fenos/rally"
~~~

### Step 4 ###

#### Include relations ###

Rally comes with some relations already setted for you, you just need to insert the `trait` that I made for you in all your models you wish to have relations with Rally.

~~~

class User extends Eloquent
{
    use \Fenos\Rally\Models\Relations;
}

~~~

That's it your have done.

## Documentation ##

How i said on the installation, Rally can be **Polymorphic**, it means that if you have `Users` and `Teams` as entity of your application they can follow between them. But it is just up to you. If you realize that
you don't need of it, You can keep it as a single model binding.

The key to enable or disable the polymorphic relation is in the configuration files. You just need to push them and change the key polymorphic to `true`.
if instead you want to keep the plugin as 1 model but the `User` model is not your main model change it ;)

~~~
php artisan config:publish fenos/rally
~~~


### Follow ###

For start to be followers of a entity when it comes polymorphically you will use the following method let me show you.

~~~
try
{
    Rally::follower('User',$user_id)->follow('Team',$team_id);
}
catch(\Fenos\Rally\Exceptions\AlreadyFollowerException $e)
{
    // is already fan
}
~~~
With only fews line of code the user has started to follow the team.

If instead you use **Rally** as normal
~~~
try
{
    Rally::follower($user_id)->follow($user_id);
}
catch(\Fenos\Rally\Exceptions\AlreadyFollowerException $e)
{
    // is already follower
}
~~~

Let me explain it. The method `follower()` specify the user that want to be follower, so if Rally comes polymorphically you have to specify as
`first paramter` The model of it, as `second parameter` the id if instead is not polymorphically just the ID.
Almost same the method `follow()` in this method you specify who will be followed parameters are same.

### UnFollow ###

If you don't want follow someone anymore you will use this method:

**Polymorphically**
~~~
try
{
    Rally::follower('User',$user_id)->unFollow('Team',$team_id);
}
catch(\Fenos\Rally\Exceptions\FollowerNotFoundException $e)
{
    // the user already doesn't follow him
}
~~~

**Normal**
~~~
try
{
    Rally::follower($user_id)->unFollow($user_id);
}
catch(\Fenos\Rally\Exceptions\FollowerNotFoundException $e)
{
    // the user already doesn't follow him
}
~~~

### Check if Is follow of ###

If you want to know a given User if has following someone use:

**Polymorphically**
~~~
Rally::follower('User',$user_id)->isFollowerOf('Team',$team_id);
~~~

**Normal**
~~~
Rally::follower($user_id)->isFollowerOf($user_id); // return Boolean
~~~

### Get lists of followers ###

Well Rally give to you a easy way to get the lists of your followers but remeber that you implemented the `trait` with the relations
in your model, So you can even access to them directly from that, I suggest that. But let me show you if you want use Rally.

**Polymorphically**
~~~
Rally::follower('User',$user_id)->getLists();

Rally::follower('User',$user_id)->getLists(['orderBy' => 'DESC', 'limit' => 10]);

Rally::follower('User',$user_id)->getLists(['orderBy' => 'DESC', 'paginate' => 5 ]);
~~~

**Normal**
~~~
Rally::follower($user_id)->getLists();

Rally::follower($user_id)->getLists(['orderBy' => 'DESC', 'limit' => 10]);

Rally::follower($user_id)->getLists(['orderBy' => 'DESC', 'paginate' => 5 ]);
~~~

You can even chain `count()` it return a Collection so you can use all the methods of it.

#### Count Followers ####

You Need just the numbers of followers and nothing else?

**Polymorphically**
~~~
Rally::follower('User',$user_id)->count();
~~~

**Normal**
~~~
Rally::follower($user_id)->count();
~~~

I hope you'll enjoy it.

### Note ###

I made it with <3

### Tests ###

For run the tests make sure to have phpUnit and Mockery installed

### Credits ###

© Copyright Fabrizio Fenoglio

Released package under MIT Licence.
