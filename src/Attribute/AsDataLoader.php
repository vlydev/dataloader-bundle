<?php

/*
 * This file is part of the OverblogDataLoaderBundle package.
 *
 * (c) Overblog <http://github.com/overblog/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Overblog\DataLoaderBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class AsDataLoader
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $method = null,
        public readonly ?string $alias = null,
        public readonly ?array $options = [],
    ) {
    }
}
