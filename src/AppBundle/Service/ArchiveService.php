<?php

namespace AppBundle\Service;

use AppBundle\Entity\Archive;
use Doctrine\Bundle\DoctrineBundle\Registry;

class ArchiveService
{

    /** @var  Registry */
    private $doctrine;

    public function __construct(
        Registry $doctrine
    ) {
        $this->doctrine = $doctrine;
    }

    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    public function create($fname, $contentStream)
    {

        $archive = new Archive();
        $archive->setFname($fname);
        $archive->setContent($contentStream);

        $em = $this->doctrine->getManager();
        $em->persist($archive);
        $em->flush();

        return $archive;

    }

    public function delete($id)
    {

        $archive = $this->find($id);

        if ($archive) {
            $em = $this->doctrine->getManager();
            $em->remove($archive);
            $em->flush();
        }

    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function getRepository()
    {
        return $this->doctrine->getRepository('AppBundle:Archive');
    }

}
