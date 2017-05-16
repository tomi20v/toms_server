<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller\ArchiveController;
use AppBundle\Entity\Archive;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArchiveControllerTest extends WebTestCase
{

    /** @var \Doctrine\Common\Persistence\ObjectRepository|\PHPUnit_Framework_MockObject_MockObject */
    private $repository;
    /** @var \Doctrine\Common\Persistence\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    private $manager;
    /** @var \Doctrine\Bundle\DoctrineBundle\Registry|\PHPUnit_Framework_MockObject_MockObject */
    private $doctrine;
    /** @var ArchiveController|\PHPUnit_Framework_MockObject_MockObject $controller */
    private $controller;

    public function setUp()
    {
        $this->repository = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectRepository')->getMock();

        $this->manager = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')->getMock();
        $this->manager->method('getRepository')
            ->with('AppBundle:Archive')
            ->willReturn($this->repository);

        $this->doctrine = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->doctrine->method('getManager')
            ->willReturn($this->manager);

        $this->controller = $this->getMockBuilder('AppBundle\Controller\ArchiveController')
            ->disableOriginalConstructor()
            ->setMethods(['getDoctrine'])
            ->getMock();

        $this->controller->method('getDoctrine')
            ->willReturn($this->doctrine);

    }

    public function testIndexAction()
    {

        $anyArray = [new Archive()];

        $this->repository->method('findAll')
            ->willReturn($anyArray);

        $result = $this->controller->indexAction();
        $this->assertSame($result, $anyArray);

    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage anyMessage
     */
    public function testIndexActionShouldThrow()
    {

        $e = new \Exception('anyMessage');
        $this->repository->method('findAll')
            ->willThrowException($e);

        $this->controller->indexAction();

    }

}
