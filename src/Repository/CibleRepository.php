<?php

namespace App\Repository;

use App\Entity\Cible;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cible>
 *
 * @method Cible|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cible|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cible[]    findAll()
 * @method Cible[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CibleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cible::class);
    }

    public function add(Cible $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Cible $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Get the total number of elements
    public function countCible()
    {
        return $this
            ->createQueryBuilder('cible')
            ->select("count(cible.id)")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('cible');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('cible');
        $countQuery->select('COUNT(cible)');

        // Create inner joins
        $query
            ->join('cible.nationalite', 'pays')
            ->join('cible.mission', 'mission');
        
        $countQuery
            ->join('cible.nationalite', 'pays')
            ->join('cible.mission', 'mission');
        
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
                    case 'cible':
                        {
                            $searchQuery = 'cible.nom LIKE \'%'.$searchItem.'%\'';
                            break;
                        }

                    case 'code':
                        {
                            $searchQuery = 'cible.code LIKE \'%'.$searchItem.'%\'';
                            break;
                        }

                    case 'mission':
                        {
                            $searchQuery = 'mission.titre LIKE \'%'.$searchItem.'%\'';
                            break;
                        }

                    case 'nationalite':
                        {
                            $searchQuery = 'pays.nom LIKE \'%'.$searchItem.'%\'';
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
                    case 'cible':   
                        $orderColumn = 'cible.nom';
                        break;
                        

                    case 'code':
                        $orderColumn = 'cible.nom_code';
                        break;
                        

                    case 'date_naissance':
                        $orderColumn = 'cible.date_naissance';
                        break;

                    case 'pays':
                        $orderColumn = 'pays.nom';
                        break;    

                    case 'mission':
                        $orderColumn = 'mission.titre';
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
//     * @return Cible[] Returns an array of Cible objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Cible
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
