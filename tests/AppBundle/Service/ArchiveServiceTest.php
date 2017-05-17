<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Archive;
use AppBundle\Service\ArchiveService;

class ArchiveServiceTest extends \PHPUnit_Framework_TestCase
{

    private $anyFname = 'any fname';

    /** @var \Doctrine\Common\Persistence\ObjectRepository|\PHPUnit_Framework_MockObject_MockObject */
    private $repository;
    /** @var \Doctrine\Common\Persistence\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    private $manager;
    /** @var \Doctrine\Bundle\DoctrineBundle\Registry|\PHPUnit_Framework_MockObject_MockObject */
    private $doctrine;
    /** @var ArchiveService|\PHPUnit_Framework_MockObject_MockObject */
    private $service;

    private $anyId = 1234;
    /** @var  resource */
    private $anyResource;

    public function setUp()
    {

        $this->repository = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')
            ->getMock();

        $this->doctrine = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->doctrine->method('getRepository')
            ->willReturn($this->repository);
        $this->doctrine->method('getManager')
            ->willReturn($this->manager);

        $this->service = new ArchiveService($this->doctrine);

        $this->anyResource = fopen('data://text/plain,' . '', 'r');

    }

    public function testFindAll()
    {

        $this->repository->expects($this->once())->method('findAll');
        $this->service->findAll();
    }

    public function testFind()
    {

        $this->repository->expects($this->once())->method('find')->with($this->anyId);
        $this->service->find($this->anyId);
    }

    public function testCreate()
    {

        $this->manager->expects($this->atLeastOnce())
            ->method('persist')
            ->willReturnCallback(function(Archive $archive) {
                $this->assertEquals($this->anyFname, $archive->getFname());
                $this->assertSame($this->anyResource, $archive->getContent());
            });
        $this->manager->expects($this->once())
            ->method('flush');

        $this->service->create($this->anyFname, $this->anyResource);

    }

    function testDelete()
    {

        $anyArchive = new Archive();
        $anyArchive->setId($this->anyId);

        /** @var ArchiveService|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMockBuilder('AppBundle\Service\ArchiveService')
            ->setMethods(['find'])
            ->setConstructorArgs([$this->doctrine])
            ->getMock();

        $service->method('find')
            ->with($this->anyId)
            ->willReturn($anyArchive);
        $this->manager->expects($this->once())
            ->method('remove');
        $this->manager->expects($this->once())
            ->method('flush');

        $service->delete($this->anyId);

    }

    function testDeleteShouldNotDeleteIfNotFound()
    {

        /** @var ArchiveService|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMockBuilder('AppBundle\Service\ArchiveService')
            ->setMethods(['find'])
            ->setConstructorArgs([$this->doctrine])
            ->getMock();

        $service->method('find')
            ->with($this->anyId)
            ->willReturn(null);
        $this->manager->expects($this->never())
            ->method('remove');
        $this->manager->expects($this->never())
            ->method('flush');

        $service->delete($this->anyId);

    }

}
