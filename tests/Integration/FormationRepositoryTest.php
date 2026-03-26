<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\FormationRepository;

class FormationRepositoryTest extends KernelTestCase
{
    public function testFindAll(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $repo = $container->get(FormationRepository::class);

        $formations = $repo->findAll();

        $this->assertIsArray($formations);
    }
}
