<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Exception\UserCreationException;
use App\Exception\UserDeletionException;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByGoogleId(string $googleId): ?User
    {
        return $this->findOneBy(['googleId' => $googleId]);
    }

    public function create(User $user): User
    {
        try {
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();

            return $user;
        } catch (\Throwable) {
            throw new UserCreationException();
        }
    }

    /**
     * @param User $user
     * @return void
     * @throws UserDeletionException
     */
    public function delete(User $user): void
    {
        try {
            $this->getEntityManager()->remove($user);
            $this->getEntityManager()->flush();
        } catch (\Throwable $e) {
            throw new UserDeletionException();
        }
    }
}
