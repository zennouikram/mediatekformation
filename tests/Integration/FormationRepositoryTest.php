<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\FormationRepository;
use App\Entity\Formation;

class FormationRepositoryTest extends KernelTestCase
{
    private FormationRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = static::getContainer()->get(FormationRepository::class);
    }

    public function testFindAll(): void
    {
        $formations = $this->repository->findAll();

        $this->assertIsArray($formations);
        foreach ($formations as $formation) {
            $this->assertInstanceOf(Formation::class, $formation);
        }
    }

    public function testFindAllOrderByPublishedAtDesc(): void
    {
        $formations = $this->repository->findAllOrderBy('publishedAt', 'DESC');

        $this->assertIsArray($formations);
        
        if (count($formations) > 1) {
            for ($i = 0; $i < count($formations) - 1; $i++) {
                $current = $formations[$i]->getPublishedAt();
                $next = $formations[$i + 1]->getPublishedAt();
                
                if ($current !== null && $next !== null) {
                    $this->assertGreaterThanOrEqual($next, $current);
                }
            }
        }
    }

    public function testFindAllOrderByTitleAsc(): void
    {
        $formations = $this->repository->findAllOrderBy('title', 'ASC');

        $this->assertIsArray($formations);
        
        if (count($formations) > 1) {
            for ($i = 0; $i < count($formations) - 1; $i++) {
                $current = $formations[$i]->getTitle();
                $next = $formations[$i + 1]->getTitle();
                
                if ($current !== null && $next !== null) {
                    $this->assertLessThanOrEqual($next, $current);
                }
            }
        }
    }

    public function testFindAllLasted(): void
    {
        $nb = 2;
        $formations = $this->repository->findAllLasted($nb);

        $this->assertIsArray($formations);
        $this->assertLessThanOrEqual($nb, count($formations));
        
        foreach ($formations as $formation) {
            $this->assertInstanceOf(Formation::class, $formation);
        }
    }

    public function testFindByContainValueWithEmptyValue(): void
    {
        $formations = $this->repository->findByContainValue('title', '');

        $this->assertIsArray($formations);
        $allFormations = $this->repository->findAll();
        $this->assertCount(count($allFormations), $formations);
    }

    public function testFindByContainValueWithTitle(): void
    {
        $formations = $this->repository->findByContainValue('title', 'test');

        $this->assertIsArray($formations);
        
        foreach ($formations as $formation) {
            $this->assertInstanceOf(Formation::class, $formation);
        }
    }

    public function testFindAllForOnePlaylist(): void
    {
        $formations = $this->repository->findAll();
        
        if (count($formations) > 0) {
            $formationWithPlaylist = null;
            foreach ($formations as $formation) {
                if ($formation->getPlaylist() !== null) {
                    $formationWithPlaylist = $formation;
                    break;
                }
            }
            
            if ($formationWithPlaylist !== null) {
                $playlistId = $formationWithPlaylist->getPlaylist()->getId();
                $playlistFormations = $this->repository->findAllForOnePlaylist($playlistId);
                
                $this->assertIsArray($playlistFormations);
                $this->assertGreaterThan(0, count($playlistFormations));
                
                foreach ($playlistFormations as $formation) {
                    $this->assertInstanceOf(Formation::class, $formation);
                    $this->assertEquals($playlistId, $formation->getPlaylist()->getId());
                }
            }
        }
        
        $this->assertTrue(true);
    }
}
