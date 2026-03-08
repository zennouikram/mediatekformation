<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Playlist>
 */
class PlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    public function add(Playlist $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function remove(Playlist $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }
    
    /**
     * Retourne toutes les playlists triées sur le nom de la playlist
     * @param type $champ
     * @param type $ordre
     * @return Playlist[]
     */
    public function findAllOrderByName(string $ordre): array{
        return $this->createQueryBuilder('p')
                ->leftJoin('p.formations', 'f')
                ->groupBy('p.id')
                ->orderBy('p.name', $ordre)
                ->getQuery()
                ->getResult();
    }

    /**
     * Return all playlists sorted by the number of formations they contain.
     * @param string $ordre 'ASC' or 'DESC'
     * @return Playlist[]
     */
    public function findAllOrderByFormationsCount(string $ordre): array
    {
        return $this->createQueryBuilder('p')
                ->leftJoin('p.formations', 'f')
                ->groupBy('p.id')
                ->orderBy('COUNT(f.id)', $ordre)
                ->getQuery()
                ->getResult();
    }
    
    /**
     * Enregistrements dont un champ contient une valeur
     * ou tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @param type $table si $champ dans une autre table
     * @return Playlist[]
     */
    public function findByContainValue($champ, $valeur, $table=""): array{
        if($valeur==""){
            return $this->findAllOrderByName('ASC');
        }
        if($table==""){
            return $this->createQueryBuilder('p')
                    ->leftjoin('p.formations', 'f')
                    ->where('p.'.$champ.' LIKE :valeur')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->groupBy('p.id')
                    ->orderBy('p.name', 'ASC')
                    ->getQuery()
                    ->getResult();
        }else{
            return $this->createQueryBuilder('p')
                    ->leftjoin('p.formations', 'f')
                    ->leftjoin('f.categories', 'c')
                    ->where('c.'.$champ.' LIKE :valeur')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->groupBy('p.id')
                    ->orderBy('p.name', 'ASC')
                    ->getQuery()
                    ->getResult();
        }
    }
    
    /**
     * Get the count of formations for a specific playlist
     * @param int $playlistId
     * @return int
     */
    public function countFormationsForPlaylist(int $playlistId): int
    {
        return (int) $this->createQueryBuilder('p')
                ->select('COUNT(f.id)')
                ->leftJoin('p.formations', 'f')
                ->where('p.id = :id')
                ->setParameter('id', $playlistId)
                ->getQuery()
                ->getSingleScalarResult();
    }
    
}
