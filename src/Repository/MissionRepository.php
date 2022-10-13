<?php

namespace App\Repository;

use App\Entity\Mission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mission>
 *
 * @method Mission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mission[]    findAll()
 * @method Mission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mission::class);
    }

    public function add(Mission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Mission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    
    // Get the total number of elements
    public function countMission()
    {
        return $this
            ->createQueryBuilder('mission')
            ->select("count(mission.id)")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('mission');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('mission');
        $countQuery->select('COUNT(mission)');

        // Create inner joins
        $query
            ->join('mission.pays', 'pays')
            ->join('mission.statut', 'statut');
        
        $countQuery
            ->join('mission.pays', 'pays')
            ->join('mission.statut', 'statut');
        
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
                    case 'titre':
                        {
                            $searchQuery = 'mission.titre LIKE \'%'.$searchItem.'%\'';
                            break;
                        }

                    case 'description':
                        {
                            $searchQuery = 'mission.description LIKE \'%'.$searchItem.'%\'';
                            break;
                        }

                    case 'statut':
                        {
                            $searchQuery = 'statut.nom LIKE \'%'.$searchItem.'%\'';
                            break;
                        }

                    case 'pays':
                        {
                            $searchQuery = 'pays.nom LIKE \'%'.$searchItem.'%\'';
                            break;
                        }

                    case 'nom_code':
                        {
                            $searchQuery = 'mission.nom_code LIKE \'%'.$searchItem.'%\'';
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
                    case 'titre':   
                        $orderColumn = 'mission.titre';
                        break;
                        

                    case 'description':
                        $orderColumn = 'mission.description';
                        break;
                        

                    case 'code_nom':
                        $orderColumn = 'mission.code_nom';
                        break;

                    case 'date_debut':
                        $orderColumn = 'mission.date_debut';
                        break;

                    case 'date_fin':
                        $orderColumn = 'mission.date_fin';
                        break;

                    case 'statut':
                        $orderColumn = 'statut.nom';
                        break;    

                    case 'pays':
                        $orderColumn = 'pays.nom';
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
//     * @return Mission[] Returns an array of Mission objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Mission
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
