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

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLastPreviousDay(Company $company): CompanyShare
    {
        return  $this->createQueryBuilder('s')
            ->where('s.company = :company')
            ->andWhere('s.createdAt >= :today')
            ->setParameter('today', date('Y-m-d'))
            ->setParameter('company', $company)
            ->select('s')
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLast(Company $company): CompanyShare
    {
        return  $this->createQueryBuilder('s')
            ->where('s.company = :company')
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

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findAvgPriceFromLastSevenDays(Company $company): array
    {

        $sql =  'SELECT AVG(s.price), s.created_at::date as created
                FROM app_company_shares as s 
                WHERE s.company_id = ? AND s.created_at BETWEEN ? AND ? 
                GROUP BY created
                ORDER BY created ASC';

        $stmt =$this->_em->getConnection()->prepare($sql);
        $stmt->bindValue(1, $company->getId());
        $stmt->bindValue(2, date('Y-m-d', strtotime('-6 days')));
        $stmt->bindValue(3, date('Y-m-d', strtotime('+1 day')));
        $stmt->execute();

        return $stmt->fetchAll();
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
