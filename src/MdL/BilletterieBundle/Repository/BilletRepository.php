<?php

namespace MdL\BilletterieBundle\Repository;

/**
 * BilletRepository
 *
 */
class BilletRepository extends \Doctrine\ORM\EntityRepository
{
    public function countBydtVisite($dtvisite)
    {
        $qb = $this
            ->createQueryBuilder('b')
            ->join('b.commande', 'c')
            ->addSelect('c')
            ->where('c.dtVisite = :dtvisite')
            ->setParameter('dtvisite', $dtvisite);

        return (int)count($qb->getQuery()->getResult());
    }
}
