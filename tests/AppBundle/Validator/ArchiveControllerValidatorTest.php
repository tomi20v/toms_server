<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Validator\ArchiveControllerValidator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArchiveControllerValidatorTest extends \PHPUnit_Framework_TestCase
{

    /** @var ArchiveControllerValidator */
    private $validator;

    /** @var  UploadedFile|\PHPUnit_Framework_MockObject_MockObject */
    private $anyUploadedFile;

    public function setUp()
    {

        $this->anyUploadedFile = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock();

        $this->validator = new ArchiveControllerValidator();

    }

    public function testValidateUploadedFile()
    {

        $this->anyUploadedFile->method('getError')
            ->willReturn(0);

        $this->validator->validateUploadedFile($this->anyUploadedFile);

    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage no file
     */
    public function testValidateUploadedFileThrowsOnNonFile()
    {

        $this->validator->validateUploadedFile(null);

    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage any message
     */
    public function testValidateUploadedFileThrowsOnError()
    {

        $anyMessage = 'any message';

        $this->anyUploadedFile->method('getError')
            ->willReturn(1);
        $this->anyUploadedFile->method('getErrorMessage')
            ->willReturn($anyMessage);

        $this->validator->validateUploadedFile($this->anyUploadedFile);

    }

    /**
     * @dataProvider fnameProvider
     */
    public function testValidateFname($fname, $valid)
    {
        try {
            $this->validator->validateFname($fname);
            $this->assertTrue($valid);
        }
        catch (\Exception $e) {
            $this->assertFalse($valid);
        }
    }

    public function fnameProvider()
    {
        return [
            ['asd', false],
            ['asd.pdf', true],
            ['asd.pdfx', false],
            ['asdpdf', false],
        ];
    }

}
