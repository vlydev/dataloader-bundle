<?php

/*
 * This file is part of the OverblogDataLoaderBundle package.
 *
 * (c) Overblog <http://github.com/overblog/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Overblog\DataLoaderBundle\Tests\Functional;

use Overblog\DataLoader\DataLoader;
use Overblog\DataLoaderBundle\Tests\Functional\app\UserDataStaticProvider;
use PHPUnit\Framework\Attributes\DataProvider;

use function React\Promise\all;

class UserLoaderTest extends TestCase
{
    public static function dataLoaderAliasesProvider(): array
    {
        return [
            ['yaml_users_loader'],
            ['class_attribute_users_loader'],
            ['method_attribute_users_loader'],
        ];
    }

    #[DataProvider('dataLoaderAliasesProvider')]
    public function testGetUsers(string $aliasName)
    {
        /** @var DataLoader $userLoader */
        $userLoader = static::getContainer()->get($aliasName);

        $promise = all([
            $userLoader->load(3),
            $userLoader->load(5),
            $userLoader->loadMany([5, 2, 4]),
            $userLoader->loadMany([1, 6, 3]),
        ]);

        $this->assertEquals(
            [
                UserDataStaticProvider::$users[3],
                UserDataStaticProvider::$users[5],
                [
                    UserDataStaticProvider::$users[5],
                    UserDataStaticProvider::$users[2],
                    UserDataStaticProvider::$users[4],
                ],
                [
                    UserDataStaticProvider::$users[1],
                    UserDataStaticProvider::$users[6],
                    UserDataStaticProvider::$users[3],
                ],
            ],
            $userLoader->await($promise)
        );
    }
}
