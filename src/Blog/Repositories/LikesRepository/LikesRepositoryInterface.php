<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\LikesRepository;

use GeekBrains\LevelTwo\Blog\Like;
use GeekBrains\LevelTwo\Blog\UUID;

interface LikesRepositoryInterface
{
    public function save(Like $like) : void;
    public function getByPostUuid(UUID $uuid) : array;
}