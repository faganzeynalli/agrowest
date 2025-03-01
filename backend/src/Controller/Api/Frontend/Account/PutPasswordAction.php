<?php

declare(strict_types=1);

namespace App\Controller\Api\Frontend\Account;

use App\Container\EntityManagerAwareTrait;
use App\Controller\SingleActionInterface;
use App\Entity\Api\Error;
use App\Entity\Api\Status;
use App\Http\Response;
use App\Http\ServerRequest;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

final class PutPasswordAction implements SingleActionInterface
{
    use EntityManagerAwareTrait;

    public function __invoke(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $user = $request->getUser();
        $body = (array)$request->getParsedBody();

        try {
            if (empty($body['current_password'])) {
                throw new InvalidArgumentException('Cari parol təmin edilməyib (current_password).');
            }

            $currentPassword = $body['current_password'];
            if (!$user->verifyPassword($currentPassword)) {
                throw new InvalidArgumentException('Cari parol səhvdir.');
            }

            if (empty($body['new_password'])) {
                throw new InvalidArgumentException('Yeni parol təmin edilməyib (new_password).');
            }

            $user = $this->em->refetch($user);

            $user->setNewPassword($body['new_password']);
            $this->em->persist($user);
            $this->em->flush();

            return $response->withJson(Status::updated());
        } catch (Throwable $e) {
            return $response->withStatus(400)->withJson(Error::fromException($e));
        }
    }
}
