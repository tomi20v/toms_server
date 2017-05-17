<?php

namespace AppBundle\Validator;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArchiveControllerValidator
{

    /**
     * @param UploadedFile $file
     * @throws \Exception
     */
    public function validateUploadedFile($file)
    {

        if (!$file instanceof UploadedFile) {
            throw new \Exception('no file');
        }
        elseif ($file->getError() !== 0) {
            throw new \Exception($file->getErrorMessage());
        }

    }

    /**
     * @param string $fname
     * @throws \Exception
     */
    public function validateFname($fname)
    {
        if (!preg_match('/\.pdf$/', $fname)) {
            throw new \Exception('filename');
        }
    }

}
