<?php

/*
 * This file is part of the OverblogDataLoaderBundle package.
 *
 * (c) Overblog <http://github.com/overblog/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Overblog\DataLoaderBundle\Tests\Functional\app;

use Overblog\DataLoaderBundle\Attribute\AsDataLoader;

#[AsDataLoader(name: 'class_attribute_users', alias: 'class_attribute_users_loader')]
class UserDataAttributedLoader
{
    #[AsDataLoader(name: 'method_attribute_users', alias: 'method_attribute_users_loader')]
    public function __invoke(array $ids)
    {
        $users = [];
        foreach ($ids as $id) {
            $users[] = isset(UserDataStaticProvider::$users[$id]) ? UserDataStaticProvider::$users[$id] : null;
        }

        return \React\Promise\resolve($users);
    }
}
