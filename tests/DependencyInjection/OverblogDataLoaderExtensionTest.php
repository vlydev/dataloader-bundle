<?php

/*
 * This file is part of the OverblogDataLoaderBundle package.
 *
 * (c) Overblog <http://github.com/overblog/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Overblog\DataLoaderBundle\Tests\DependencyInjection;

use Overblog\DataLoaderBundle\DependencyInjection\Configuration;
use Overblog\DataLoaderBundle\DependencyInjection\OverblogDataLoaderExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverblogDataLoaderExtensionTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;
    /**
     * @var OverblogDataLoaderExtension
     */
    private $extension;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.bundles', []);
        $this->container->setParameter('kernel.debug', false);
        $this->extension = new OverblogDataLoaderExtension();
    }

    protected function tearDown(): void
    {
        unset($this->container, $this->extension);
    }

    public function testValidServiceCallableNodeValue()
    {
        $validValues = ['@app.user:getUsers', '@App\\Loader\\User:all'];
        foreach ($validValues as $validValue) {
            $this->assertMatchesRegularExpression(Configuration::SERVICE_CALLABLE_NOTATION_REGEX, $validValue);
        }
    }

    public function testValidPhpCallableNodeValue()
    {
        $validValues = ['Image\\Loader::get', 'Post::getPosts'];
        foreach ($validValues as $validValue) {
            $this->assertMatchesRegularExpression(Configuration::PHP_CALLABLE_NOTATION_REGEX, $validValue);
        }
    }

    public function testBatchLoadFnNotCallable()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage("\"NOT CALLABLE\" doesn't seem to be a valid callable.");

        $this->extension->load(
            [
                [
                    'defaults' => [
                        'promise_adapter' => 'overblog_dataloader.react_promise_adapter',
                    ],
                    'loaders' => [
                        'users' => [
                            'batch_load_fn' => 'NOT CALLABLE',
                        ],
                    ],
                ],
            ],
            $this->container
        );
    }
}
