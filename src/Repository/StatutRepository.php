<?php

namespace App\Repository;

use App\Entity\Statut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Statut>
 *
 * @method Statut|null find($id, $lockMode = null, $lockVersion = null)
 * @method Statut|null findOneBy(array $criteria, array $orderBy = null)
 * @method Statut[]    findAll()
 * @method Statut[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Statut::class);
    }

    public function add(Statut $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Statut $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Get the total number of elements
    public function countStatuts()
    {
        return $this
            ->createQueryBuilder('statut')
            ->select("count(statut.id)")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('statut');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('statut');
        $countQuery->select('COUNT(statut)');
        
        // Other conditions than the ones sent by the Ajax call ?
        if ($otherConditions === null)
        {
            // No
            // However, add a "always true" condition to keep an uniform treatment in all cases
            $query->where("1=1");
            $countQuery->where("1=1");
        }
        else
        {
            // Add condition
            $query->where($otherConditions);
            $countQuery->where($otherConditions);
        }
        
        // Fields Search
        foreach ($columns as $key => $column)
        {
            if ($column['search']['value'] != '')
            {
                // $searchItem is what we are looking for
                $searchItem = $column['search']['value'];
                $searchQuery = null;
        
                // $column['name'] is the name of the column as sent by the JS
                switch($column['name'])
                {
                    case 'nom':
                    {
                        $searchQuery = 'statut.nom LIKE \'%'.$searchItem.'%\'';
                        break;
                    }
                  
                }
        
                if ($searchQuery !== null)
                {
                    $query->andWhere($searchQuery);
                    $countQuery->andWhere($searchQuery);
                }
            }
        }
        
        // Limit
        $query->setFirstResult($start)->setMaxResults($length);
        
        // Order
        foreach ($orders as $key => $order)
        {
            // $order['name'] is the name of the order column as sent by the JS
            if ($order['name'] != '')
            {
                $orderColumn = null;
            
                switch($order['name'])
                {
                    case 'nom':
                    {
                        $orderColumn = 'statut.nom';
                        break;
                    }
                   
                }
        
                if ($orderColumn !== null)
                {
                    $query->orderBy($orderColumn, $order['dir']);
                }
            }
        }
        
        // Execute
        $results = $query->getQuery()->getResult();
        $countResult = $countQuery->getQuery()->getSingleScalarResult();
        
        return array(
            "results" 		=> $results,
            "countResult"	=> $countResult
        );
    }

//    /**
//     * @return Statut[] Returns an array of Statut objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Statut
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
