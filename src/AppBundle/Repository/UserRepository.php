<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * @author Devnet Product Team
 */
class UserRepository extends EntityRepository
{
    public function searchByRole($role)
    {
        $builder = $this->findByRoleBuilder($role);
        return self::getPaginator($builder);
    }

    public function findByRole($role)
    {
        $builder = $this->findByRoleBuilder($role);
        return $builder->getQuery()->getResult();
    }

    /**
     * @param $role
     *
     * @return QueryBuilder
     */
    private function findByRoleBuilder($role) {

        return $this->createQueryBuilder('u')
                    ->where('u.roles LIKE :roles')
                    ->setParameter('roles', '%"ROLE_' . strtoupper($role) . '"%');
    }

} 