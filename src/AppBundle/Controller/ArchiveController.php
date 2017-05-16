<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Archive;
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
            // @todo filter results either here or in the serializer so it doesn't return the 'content' field
            $archives = $this->getRepository()->findAll();
            return $archives;
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
        try {

            /** @var Archive $archive */
            $archive = $this->getDoctrine()->getRepository('AppBundle:Archive')->find($id);
            if (!$archive) {
                throw new NotFoundHttpException();
            }

            /** @var resource $content */
            $content = $archive->getContent();

            return new Response(
                stream_get_contents($content),
                200,
                ['Content-Type' => 'application/pdf']
            );

        }
        catch (HttpException $e) {
            throw $e;
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @Rest\Post("api/archive/")
     * @throws \Exception
     */
    public function createAction(Request $request)
    {
        return $this->wrapLogic(function() use ($request) {

            $file = $request->files->get('file');
            if (!$file instanceof UploadedFile) {
                throw new \Exception('no file');
            }
            elseif ($file->getError() !== 0) {
                throw new \Exception($file->getErrorMessage());
            }
            $fname = $file->getClientOriginalName();
            if (empty($fname)) {
                throw new \Exception('filename');
            }
            // @todo here I should check the file type
            $fp = fopen($file->getPathname(), 'rb');
            if (!$fp) {
                throw new \Exception('internal');
            }

            $archive = new Archive();
            $archive->setFname($fname);
            $archive->setContent($fp);

            $em = $this->getManager();
            $em->persist($archive);
            $em->flush();

            return true;

        });
    }

    /**
     * @Rest\Delete("api/archive/{id}", requirements={"id" = "\d+"})
     */
    public function deleteAction($id)
    {
        return $this->wrapLogic(function() use ($id) {
            $archive = $this->getRepository()->find($id);
            if (!$archive) {
                throw new NotFoundHttpException();
            }
            $em = $this->getManager();
            $em->remove($archive);
            $em->flush();
        });
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
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    private function getManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function getRepository()
    {
        return $this->getManager()->getRepository('AppBundle:Archive');
    }

}
