<?php

/*
 * This file is part of the OverblogDataLoaderBundle package.
 *
 * (c) Overblog <http://github.com/overblog/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Overblog\DataLoaderBundle\Tests\DependencyInjection\CompilerPass;

use Overblog\DataLoaderBundle\DependencyInjection\CompilerPass\RegisterDataLoadersFromTagsPass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterDataLoadersFromTagsPassTest extends TestCase
{
    public static function dataProvider(): array
    {
        $tagCreatedFromYaml = [
            'name' => 'dataloader1',
            'alias' => 'dataloader1_alias',
            'batch_load_fn' => '@service1:load',
            'promise_adapter' => 'overblog_dataloader.react_promise_adapter',
            'factory' => '@factory1:create',
            'options' => [
                'cache_map' => 'overblog_dataloader.cache_map',
                'cache_key_fn' => null,
                'max_batch_size' => 10,
                'cache' => true,
                'batch' => true,
            ],
        ];

        $tagCreatedFromAttribute = [
            'name' => 'dataloader1',
            'alias' => 'dataloader1_alias',
            'method' => 'load',
            'promise_adapter' => 'overblog_dataloader.react_promise_adapter',
            'factory' => '@factory1:create',
            'options' => [
                'cache_map' => 'overblog_dataloader.cache_map',
                'cache_key_fn' => null,
                'max_batch_size' => 10,
                'cache' => true,
                'batch' => true,
            ],
        ];

        return [
            [$tagCreatedFromYaml, [new Reference('service1'), 'load']],
            [$tagCreatedFromAttribute, [new Reference('dataloader1'), 'load']],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testCreatesDataLoaderFromBatchLoadFn(array $tagProperties, array $batchLoadFn): void
    {
        $builder = new ContainerBuilder();
        $builder->register('dataloader1', \stdClass::class)->addTag('overblog.dataloader', $tagProperties);

        (new RegisterDataLoadersFromTagsPass())->process($builder);

        // Check if service is registered
        $this->assertTrue($builder->has('overblog_dataloader.dataloader1_loader'));
        $this->assertTrue($builder->has('dataloader1_alias'));

        // Check if correct arguments are passed
        $arguments = $builder->getDefinition('overblog_dataloader.dataloader1_loader')->getArguments();
        $this->assertCount(3, $arguments);
        $this->assertEquals($batchLoadFn, $arguments[0]); // batch load function
        $this->assertEquals(new Reference('overblog_dataloader.react_promise_adapter'), $arguments[1]); // promise adapter
        $this->assertEquals(new Reference('overblog_dataloader.dataloader1_loader_option'), $arguments[2]); // options

        // Check if factory is set
        $factory = $builder->getDefinition('overblog_dataloader.dataloader1_loader')->getFactory();
        $this->assertEquals([new Reference('factory1'), 'create'], $factory);
    }
}
