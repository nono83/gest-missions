<?php

namespace App\Repository;

use App\Entity\Planque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Planque>
 *
 * @method Planque|null find($id, $lockMode = null, $lockVersion = null)
 * @method Planque|null findOneBy(array $criteria, array $orderBy = null)
 * @method Planque[]    findAll()
 * @method Planque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planque::class);
    }

    public function add(Planque $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Planque $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     // Get the total number of elements
     public function countPlanque()
     {
         return $this
             ->createQueryBuilder('planque')
             ->select("count(planque.id)")
             ->getQuery()
             ->getSingleScalarResult();
     }
 
     public function getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions)
     {
         // Create Main Query
         $query = $this->createQueryBuilder('planque');
         
         // Create Count Query
         $countQuery = $this->createQueryBuilder('planque');
         $countQuery->select('COUNT(planque)');
 
         // Create inner joins
         $query
             ->join('planque.type_planque', 't')
             ->join('planque.mission', 'm');
         
         $countQuery
            ->join('planque.type_planque', 't')
            ->join('planque.mission', 'm');
         
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
                     case 'code':
                         {
                             $searchQuery = 'planque.code LIKE \'%'.$searchItem.'%\'';
                             break;
                         }
 
                     case 'adresse':
                         {
                             $searchQuery = 'planque.adresse LIKE \'%'.$searchItem.'%\'';
                             break;
                         }
 
                     case 'type_planque':
                         {
                             $searchQuery = 't.nom LIKE \'%'.$searchItem.'%\'';
                             break;
                         }
 
                     case 'mission':
                         {
                             $searchQuery = 'm.titre LIKE \'%'.$searchItem.'%\'';
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
                     case 'code':   
                         $orderColumn = 'planque.code';
                         break;
                         
 
                     case 'adresse':
                         $orderColumn = 'planque.adresse';
                         break;
                         
 
                     case 'type_planque':
                         $orderColumn = 't.nom';
                         break;
 
                     case 'mission':
                         $orderColumn = 'm.titre';
                         break;
 
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
//     * @return Planque[] Returns an array of Planque objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Planque
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
