<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\ArchiveController;
use AppBundle\Entity\Archive;
use AppBundle\Service\ArchiveService;
use AppBundle\Validator\ArchiveControllerValidator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ArchiveControllerTest extends \PHPUnit_Framework_TestCase
{

    /** @var ArchiveService|\PHPUnit_Framework_MockObject_MockObject */
    private $archiveService;
    /** @var ArchiveControllerValidator|\PHPUnit_Framework_MockObject_MockObject */
    private $validator;
    /** @var ArchiveController|\PHPUnit_Framework_MockObject_MockObject */
    private $controller;

    private $anyFname = 'any fname';
    private $anyId = 1234;
    /** @var  resource */
    private $anyResource;
    /** @var \Exception */
    private $anyException;
    /** @var  UploadedFile|\PHPUnit_Framework_MockObject_MockObject */
    private $anyUploadedFile;
    /** @var  FileBag|\PHPUnit_Framework_MockObject_MockObject */
    private $anyFiles;
    /** @var  Request|\PHPUnit_Framework_MockObject_MockObject */
    private $anyRequest;

    public function setUp()
    {

        $this->archiveService = $this->getMockBuilder('AppBundle\Service\ArchiveService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->validator = $this->getMockBuilder('AppBundle\Validator\ArchiveControllerValidator')
            ->getMock();

        $this->controller = $this->getMockBuilder('AppBundle\Controller\ArchiveController')
            ->disableOriginalConstructor()
            ->setMethods(['get', 'streamGetContents', 'getValidator', 'fopen'])
            ->getMock();
        $this->controller->method('get')
            ->willReturnMap([
                ['app_bundle.archive_service', $this->archiveService],
                ['app_bundle.archive_controller_validator', $this->validator],
            ]);

        // I'll mock out stream_get_contents but I need a resource to check call params
        $this->anyResource = fopen('data://text/plain,' . '', 'r');

        $this->anyException = new \Exception('anyMessage');

    }

    public function testIndexAction()
    {

        $anyArray = [new Archive()];

        $this->archiveService
            ->expects($this->once())
            ->method('findAll')
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
        $this->archiveService->method('findAll')
            ->willThrowException($e);

        $this->controller->indexAction();

    }

    public function testShowAction()
    {

        $anyContent = 'any content';

        $anyArchive = new Archive();
        $anyArchive->setId($this->anyId);
        $anyArchive->setContent($this->anyResource);

        $this->archiveService
            ->expects($this->once())
            ->method('find')
            ->with($this->anyId)
            ->willReturn($anyArchive);
        $this->controller
            ->expects($this->once())
            ->method('streamGetContents')
            ->with($this->anyResource)
            ->willReturn($anyContent);

        /** @var Response $result */
        $result = $this->controller->showAction($this->anyId);

        $this->assertTrue($result instanceof Response);
        $this->assertEquals($anyContent, $result->getContent());
        $this->assertEquals(200, $result->getStatusCode());
        /** @var ResponseHeaderBag $headers */
        $headers = $result->headers;
        $this->assertTrue($headers->contains('Content-Type', 'application/pdf'));

    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage anyMessage
     */
    public function testShowActionShouldThrow()
    {

        $this->archiveService->method('find')
            ->with($this->anyId)
            ->willThrowException($this->anyException);

        $this->controller->showAction($this->anyId);

    }

    public function testCreateAction()
    {

        $this->setUpCreateAction();

        $this->anyFiles->method('get')
            ->willReturn($this->anyUploadedFile);
        $this->anyUploadedFile->method('getClientOriginalName')
            ->willReturn($this->anyFname);

        $this->validator->expects($this->once())
            ->method('validateUploadedFile')
            ->with($this->anyUploadedFile);
        $this->validator->expects($this->once())
            ->method('validateFname')
            ->with($this->anyFname);

        $this->controller->method('fopen')
            ->willReturn($this->anyResource);

        $this->archiveService->expects($this->once())
            ->method('create')
            ->with($this->anyFname, $this->anyResource);

        $result = $this->controller->createAction($this->anyRequest);
        $this->assertEquals(true, $result);

    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage any message
     */
    public function testCreateActionShouldThrowIfFileValidatorThrows()
    {

        $this->setUpCreateAction();

        $this->anyFiles->method('get')
            ->willReturn($this->anyUploadedFile);
        $this->anyUploadedFile->method('getClientOriginalName')
            ->willReturn($this->anyFname);

        $this->validator->expects($this->once())
            ->method('validateUploadedFile')
            ->willThrowException(new \Exception('any message'));

        $this->controller->createAction($this->anyRequest);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage any message2
     */
    public function testCreateActionShouldThrowIfFnameValidatorThrows()
    {

        $this->setUpCreateAction();

        $this->anyFiles->method('get')
            ->willReturn($this->anyUploadedFile);
        $this->anyUploadedFile->method('getClientOriginalName')
            ->willReturn($this->anyFname);

        $this->validator->expects($this->once())
            ->method('validateFname')
            ->willThrowException(new \Exception('any message2'));

        $this->controller->createAction($this->anyRequest);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage internal
     */
    public function testCreateActionShouldThrowIfFileCannotBeRead()
    {

        $this->setUpCreateAction();

        $this->anyFiles->method('get')
            ->willReturn($this->anyUploadedFile);
        $this->controller->method('fopen')
            ->willReturn(false);

        $this->controller->createAction($this->anyRequest);

    }

    public function testDeleteAction()
    {

        $this->archiveService->expects($this->once())
            ->method('delete')
            ->with($this->anyId);

        $result = $this->controller->deleteAction($this->anyId);

        $this->assertEquals(true, $result);

    }

    private function setUpCreateAction()
    {

        $this->anyUploadedFile = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock();

        $this->anyFiles = $this->getMockBuilder('\Symfony\Component\HttpFoundation\FileBag')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $this->anyRequest = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $this->anyRequest->files = $this->anyFiles;

    }

}
