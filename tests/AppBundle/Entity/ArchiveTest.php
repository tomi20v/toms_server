<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Archive;

class EntityTest extends \PHPUnit_Framework_TestCase
{

    private $anyId = 123;
    private $anyValue = 'any value';

    public function testGetSetId()
    {
        $archive = new Archive();
        $this->assertEmpty($archive->getId());
        $this->assertSame($archive, $archive->setId($this->anyId));
        $this->assertSame($this->anyId, $archive->getId());
    }

    public function testGetSetFname()
    {
        $archive = new Archive();
        $this->assertEmpty($archive->getFname());
        $this->assertSame($archive, $archive->setFname($this->anyValue));
        $this->assertSame($this->anyValue, $archive->getFname());
    }

    public function testGetSetContent()
    {
        $archive = new Archive();
        $this->assertEmpty($archive->getId());
        $this->assertSame($archive, $archive->setContent($this->anyValue));
        $this->assertSame($this->anyValue, $archive->getContent());
    }
}
