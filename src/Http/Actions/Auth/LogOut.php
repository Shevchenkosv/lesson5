<?php

namespace GeekBrains\LevelTwo\Http\Actions\Auth;

use GeekBrains\LevelTwo\Blog\AuthToken;
use GeekBrains\LevelTwo\Blog\Exceptions\AuthException;
use GeekBrains\LevelTwo\Blog\Exceptions\AuthTokenNotFoundException;
use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use GeekBrains\LevelTwo\HTTP\Actions\ActionInterface;
use GeekBrains\LevelTwo\Http\Auth\BearerTokenAuthentication;
use GeekBrains\LevelTwo\http\Request;
use GeekBrains\LevelTwo\http\Response;
use GeekBrains\LevelTwo\Http\SuccessfulResponse;

class LogOut implements ActionInterface
{

    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository,
        private BearerTokenAuthentication $authentication
    ) {
    }

    /**
     * @throws AuthException
     */
    public function handle(Request $request): Response
    {
       $token = $this->authentication->getAuthTokenString($request);

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException $exception) {
            throw new AuthException($exception->getMessage());
        }

        $authToken->setExpiresOn(new \DateTimeImmutable("now"));


        $this->authTokensRepository->save($authToken);

        return new SuccessfulResponse([
            'token' => $authToken->token()
        ]);

    }
}