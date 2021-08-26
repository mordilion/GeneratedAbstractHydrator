<?php

/**
 * This file is part of the GeneratedAbstractHydrator package.
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 *
 * @copyright (c) Henning Huncke - <mordilion@gmx.de>
 */

declare(strict_types=1);

namespace Mordilion\GeneratedAbstractHydrator\Hydrator;

use Laminas\Hydrator\Filter;

/**
 * @author Henning Huncke <mordilion@gmx.de>
 */
trait FilterEnabledTrait
{
    private ?Filter\FilterComposite $filterComposite = null;

    public function addFilter(string $name, $filter, int $condition = Filter\FilterComposite::CONDITION_OR): void
    {
        $this->getCompositeFilter()->addFilter($name, $filter, $condition);
    }

    public function getFilter() : Filter\FilterInterface
    {
        return $this->getCompositeFilter();
    }

    public function hasFilter(string $name): bool
    {
        return $this->getCompositeFilter()->hasFilter($name);
    }

    public function removeFilter(string $name): void
    {
        $this->getCompositeFilter()->removeFilter($name);
    }

    private function getCompositeFilter() : Filter\FilterComposite
    {
        if (!$this->filterComposite) {
            $this->filterComposite = new Filter\FilterComposite();
        }

        return $this->filterComposite;
    }
}
