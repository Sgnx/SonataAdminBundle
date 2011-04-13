<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminBundle\Datagrid\ORM;

use Sonata\AdminBundle\Datagrid\Pager as BasePager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Doctrine pager class.
 *
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrinePager.class.php 28897 2010-03-30 20:30:24Z Jonathan.Wage $
 */
class Pager extends BasePager
{

    /**
     * Returns a query for counting the total results.
     *
     * @return integer
     */
    public function computeNbResult()
    {
        $countQuery = clone $this->getQuery();

        if(count($this->getParameters()) > 0) {
            $countQuery->setParameters($this->getParameters());
        }

        $countQuery->select(sprintf('count(%s.%s) as cnt', $countQuery->getRootAlias(), $this->getCountColumn()));

        return $countQuery->getQuery()->getSingleScalarResult();
    }

    /**
     * Get all the results for the pager instance
     *
     * @param mixed $hydrationMode A hydration mode identifier
     * @return array
     */
    public function getResults($hydrationMode = Query::HYDRATE_OBJECT)
    {
        return $this->getQuery()->getQuery()->execute(array(), $hydrationMode);
    }

    /**
     * Get the query for the pager.
     *
     * @return Doctrine\ORM\Query
     */

    public function getQuery()
    {
        if (!$this->query) {
            $this->query = $this->getQuery()->getQuery();
        }

        return $this->query;
    }

    public function init()
    {
        $this->resetIterator();

        $this->setNbResults($this->computeNbResult());

        $query = $this->getQuery();

        $query
            ->setFirstResult(0)
            ->setMaxResults(0);

        if (count($this->getParameters()) > 0) {
            $query->setParameters($this->getParameters());
        }

        if (0 == $this->getPage() || 0 == $this->getMaxPerPage() || 0 == $this->getNbResults()) {
            $this->setLastPage(0);
        } else {
            $offset = ($this->getPage() - 1) * $this->getMaxPerPage();

            $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));

            $query
                ->setFirstResult($offset)
                ->setMaxResults($this->getMaxPerPage());
        }
    }
}