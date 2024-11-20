<?php

namespace App\Core\User\Application\Query\GetActiveUsers;

use App\Core\User\Application\DTO\UserDTO;
use App\Core\User\Application\Query\GetActiveUsers\GetActiveUsersQuery;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use App\Core\User\Domain\User;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetActiveUsersHandler
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {}

    public function __invoke(GetActiveUsersQuery $query): array
    {
        $activeUsers = $this->userRepository->getByActive(false);

        return array_map(function (User $user) {
            return new UserDTO(
                $user->getEmail(),
                $user->isActive()
            );
        }, $activeUsers);
    }
}