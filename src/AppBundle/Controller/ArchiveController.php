<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Archive;
use AppBundle\Service\ArchiveService;
use AppBundle\Validator\ArchiveControllerValidator;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Archive controller.
 *
 * Route("api/archive")
 */
class ArchiveController extends FOSRestController
{

    /**
     * Lists all archive entities.
     *
     * @Rest\Get("api/archive/")
     * @throws \Exception
     */
    public function indexAction()
    {

        return $this->wrapLogic(function() {
            return $this->getService()->findAll();
        });

    }

    /**
     * Finds and displays a archive entity.
     *
     * Route("/{id}", name="archive_show")
     * Method("GET")
     * @Rest\Get("api/archive/{id}", requirements={"id" = "\d+"})
     */
    public function showAction($id)
    {

        return $this->wrapLogic(function() use ($id) {

            /** @var Archive $archive */
            $archive = $this->getService()->find($id);
            if (!$archive) {
                throw new NotFoundHttpException();
            }

            /** @var resource $content */
            $content = $archive->getContent();

            return new Response(
                $this->streamGetContents($content),
                200,
                ['Content-Type' => 'application/pdf']
            );

        });

    }

        /**
     * @Rest\Post("api/archive/")
     * @throws \Exception
     */
    public function createAction(Request $request)
    {

        return $this->wrapLogic(function() use ($request) {

            $file = $request->files->get('file');

            $this->getValidator()->validateUploadedFile($file);

            $fname = $file->getClientOriginalName();
            $this->getValidator()->validateFname($fname);

            $fp = $this->fopen($file);
            if (!$fp) {
                throw new \Exception('internal');
            }

            $this->getService()->create($fname, $fp);

            return true;

        });
    }

    /**
     * @Rest\Delete("api/archive/{id}", requirements={"id" = "\d+"})
     */
    public function deleteAction($id)
    {
        return $this->wrapLogic(function() use ($id) {
            $this->getService()->delete($id);
            return true;
        });
    }

    /**
     * @param resource $content
     * @return string
     */
    protected function streamGetContents($content)
    {
        return stream_get_contents($content);
    }

    /**
     * @param UploadedFile $file
     * @return resource
     */
    protected function fopen(UploadedFile $file)
    {
        return fopen($file->getPathname(), 'rb');
    }

    private function wrapLogic(callable $fn)
    {
        try {
            return $fn();
        }
        catch (HttpException $e) {
            throw $e;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return ArchiveService
     */
    private function getService()
    {
        /** @var ArchiveService $ret */
        $ret = $this->get('app_bundle.archive_service');
        return $ret;
    }

    /**
     * @return ArchiveControllerValidator
     */
    private function getValidator()
    {
        /** @var ArchiveControllerValidator $ret */
        $ret = $this->get('app_bundle.validator.archive_controller_validator');
        return $ret;
    }

}
