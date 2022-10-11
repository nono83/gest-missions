<?php

namespace App\Repository;

use App\Entity\TypePlanque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypePlanque>
 *
 * @method TypePlanque|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypePlanque|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypePlanque[]    findAll()
 * @method TypePlanque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypePlanqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypePlanque::class);
    }

    public function add(TypePlanque $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TypePlanque $entity, bool $flush = false): void
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
             ->createQueryBuilder('type_planque')
             ->select("count(type_planque.id)")
             ->getQuery()
             ->getSingleScalarResult();
     }
 
     public function getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions)
     {
         // Create Main Query
         $query = $this->createQueryBuilder('type_planque');
         
         // Create Count Query
         $countQuery = $this->createQueryBuilder('type_planque');
         $countQuery->select('COUNT(type_planque)');
         
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
                         $searchQuery = 'type_planque.nom LIKE \'%'.$searchItem.'%\'';
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
                         $orderColumn = 'type_planque.nom';
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
//     * @return TypePlanque[] Returns an array of TypePlanque objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TypePlanque
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
