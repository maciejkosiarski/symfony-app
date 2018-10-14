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
    public function findExtremesByCompany(Company $company): array
    {
        return  $this->createQueryBuilder('s')
            ->Where('s.company = :company')
            ->setParameter('company', $company)
            ->select('MAX(s.price) as max, MIN(s.price) as min')
            ->getQuery()
            ->getSingleResult();
    }

    public function findLastPreviousDay(Company $company)
    {
        return  $this->createQueryBuilder('s')
            ->where('s.company = :company')
            ->andWhere('s.createdAt BETWEEN :start AND :end')
            ->setParameter('start', date('Y-m-d', strtotime('-1 days')))
            ->setParameter('end', date('Y-m-d'))
            ->setParameter('company', $company)
            ->select('s')
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }

    public function findLastSixDays(Company $company): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.company = :company')
            ->andWhere('s.createdAt BETWEEN :last5days AND :today')
            ->setParameter('company', $company)
            ->setParameter('today', date('Y-m-d', strtotime('+1 days')))
            ->setParameter('last5days', date('Y-m-d', strtotime('-5 days')))
            ->select('s')
            ->orderBy('s.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByLastDays(Company $company, int $days): array
    {
        return  $this->createQueryBuilder('s')
            ->where('s.company = :company')
            ->setParameter('company', $company)
            ->select('s')
            ->orderBy('s.createdAt', 'ASC')
            ->setMaxResults($days * 5)
            ->getQuery()
            ->getResult();
    }
}
