<?php

namespace GeekBrains\LevelTwo\Http\Actions\Posts;

use GeekBrains\LevelTwo\Blog\Exceptions\AuthException;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\http\Actions\ActionInterface;
use GeekBrains\LevelTwo\Http\Auth\IdentificationInterface;
use GeekBrains\LevelTwo\Http\Auth\JsonBodyUsernameIdentification;
use GeekBrains\LevelTwo\http\ErrorResponse;
use GeekBrains\LevelTwo\http\Request;
use GeekBrains\LevelTwo\http\Response;
use GeekBrains\LevelTwo\http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
// Внедряем контракт логгера
        private LoggerInterface $logger,
        private IdentificationInterface $identification,


    )
    {
    }

    /**
     * @throws AuthException
     * @throws InvalidArgumentException
     */
    public function handle(Request $request): Response
    {
        $user = $this->identification->user($request);


        $newPostUuid = UUID::random();

        try {
            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->postsRepository->save($post);
        $this->logger->info("Post created: $newPostUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}