<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\CompanyShare;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CompanyShareRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CompanyShare::class);
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findExtremesByCompany(Company $company)
    {
        return  $this->createQueryBuilder('share')
            ->Where('share.company = :company')
            ->setParameter('company', $company)
            ->select('MAX(share.price) as max, MIN(share.price) as min')
            ->getQuery()
            ->getSingleResult();
    }
}
