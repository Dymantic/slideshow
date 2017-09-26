<?php

namespace Dymantic\Slideshow\Tests;

use Dymantic\Articles\AuthorsArticles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class TestUserModel extends User
{

    protected $table = 'test_users';
    protected $guarded = [];
    public $timestamps = false;



}