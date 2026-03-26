<?php

namespace App\Tests\Unit;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

class FormationTest extends TestCase
{
    public function testGetPublishedAtString(): void
    {
        $formation = new Formation();
        $date = new \DateTime('2024-03-15');
        $formation->setPublishedAt($date);
        
        $this->assertEquals('15/03/2024', $formation->getPublishedAtString());
    }

    public function testGetPublishedAtStringWithNull(): void
    {
        $formation = new Formation();
        $formation->setPublishedAt(null);
        
        $this->assertEquals('', $formation->getPublishedAtString());
    }

    public function testGetMiniature(): void
    {
        $formation = new Formation();
        $formation->setVideoId('abc123');
        
        $this->assertEquals('https://i.ytimg.com/vi/abc123/default.jpg', $formation->getMiniature());
    }

    public function testGetPicture(): void
    {
        $formation = new Formation();
        $formation->setVideoId('xyz789');
        
        $this->assertEquals('https://i.ytimg.com/vi/xyz789/hqdefault.jpg', $formation->getPicture());
    }

    public function testSetAndGetTitle(): void
    {
        $formation = new Formation();
        $formation->setTitle('Test Formation');
        
        $this->assertEquals('Test Formation', $formation->getTitle());
    }

    public function testSetAndGetDescription(): void
    {
        $formation = new Formation();
        $formation->setDescription('Test Description');
        
        $this->assertEquals('Test Description', $formation->getDescription());
    }

    public function testSetAndGetVideoId(): void
    {
        $formation = new Formation();
        $formation->setVideoId('test123');
        
        $this->assertEquals('test123', $formation->getVideoId());
    }

    public function testSetAndGetPublishedAt(): void
    {
        $formation = new Formation();
        $date = new \DateTime('2024-01-01');
        $formation->setPublishedAt($date);
        
        $this->assertEquals($date, $formation->getPublishedAt());
    }

    public function testCategoriesInitializedAsEmptyCollection(): void
    {
        $formation = new Formation();
        
        $this->assertCount(0, $formation->getCategories());
    }
}
