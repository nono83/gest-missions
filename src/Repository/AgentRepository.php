<?php

namespace App\Repository;

use App\Entity\Agent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Agent>
 *
 * @method Agent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agent[]    findAll()
 * @method Agent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agent::class);
    }

    public function add(Agent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Agent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Get the total number of elements
    public function countAgent()
    {
        return $this
            ->createQueryBuilder('agent')
            ->select("count(agent.id)")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('agent');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('agent');
        $countQuery->select('COUNT(agent)');

        // Create inner joins
        $query
            ->join('agent.nationalite', 'pays')
            ->leftJoin('agent.specialites', 'specialite')
            ->leftJoin('agent.missions', 'mission');
        
        $countQuery
            ->join('agent.nationalite', 'pays')
            ->leftJoin('agent.specialites', 'specialite')
            ->leftJoin('agent.missions', 'mission');
        
       
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
                    case 'agent':
                        {
                            $searchQuery = 'agent.nom LIKE \'%'.$searchItem.'%\'';
                            break;
                        }

                    case 'code':
                        {
                            $searchQuery = 'agent.code_identification LIKE \'%'.$searchItem.'%\'';
                            break;
                        }

                    case 'nationalite':
                        {
                            $searchQuery = 'pays.nom LIKE \'%'.$searchItem.'%\'';
                            break;
                        }

                    case 'specialite':
                        {
                            $searchQuery = 'specialite.nom LIKE \'%'.$searchItem.'%\'';
                            break;
                        }

                    case 'mission':
                        {
                            $searchQuery = 'mission.titre LIKE \'%'.$searchItem.'%\'';
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
                    case 'agent':   
                        $orderColumn = 'agent.nom';
                        break;
                        

                    case 'code':
                        $orderColumn = 'agent.code_identification';
                        break;
                        

                    case 'code_nom':
                        $orderColumn = 'agent.code_nom';
                        break;

                    case 'date_naissance':
                        $orderColumn = 'agent.date_naissance';
                        break;

                    case 'specialite':
                        $orderColumn = 'specialite.nom';
                        break;    

                    case 'mission':
                        $orderColumn = 'mission.titre';
                        break;    
    

                    case 'nationalite':
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
//     * @return Agent[] Returns an array of Agent objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Agent
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
