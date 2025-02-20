<?php
declare(strict_types = 1);
/**
 * /tests/Integration/Resource/ResourceCollectionTest.php
 *
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@pinja.com>
 */

namespace App\Tests\Integration\Resource;

use App\Entity\ApiKey;
use App\Entity\DateDimension;
use App\Entity\Healthz;
use App\Entity\Interfaces\EntityInterface;
use App\Entity\LogLogin;
use App\Entity\LogLoginFailure;
use App\Entity\LogRequest;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserGroup;
use App\Resource\ApiKeyResource;
use App\Resource\DateDimensionResource;
use App\Resource\HealthzResource;
use App\Resource\LogLoginFailureResource;
use App\Resource\LogLoginResource;
use App\Resource\LogRequestResource;
use App\Resource\ResourceCollection;
use App\Resource\RoleResource;
use App\Resource\UserGroupResource;
use App\Resource\UserResource;
use App\Rest\RestResource;
use ArrayObject;
use Generator;
use InvalidArgumentException;
use IteratorAggregate;
use LogicException;
use Psr\Log\LoggerInterface;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class ResourceCollectionTest
 *
 * @package App\Tests\Integration\Resource
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@pinja.com>
 */
class ResourceCollectionTest extends KernelTestCase
{
    public function testThatGetMethodThrowsAnException(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Resource \'FooBar\' does not exist');

        (new ResourceCollection($this->getEmptyIteratorAggregate(), $logger))
            ->get('FooBar');
    }

    public function testThatLoggerIsCalledIfGetMethodGetIteratorThrowsAnException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Resource \'FooBar\' does not exist');

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $logger
            ->expects(self::once())
            ->method('error');

        (new ResourceCollection($this->getIteratorAggregateThatThrowsAnException(), $logger))
            ->get('FooBar');
    }

    public function testThatGetEntityResourceMethodThrowsAnException(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Resource class does not exist for entity \'FooBar\'');

        (new ResourceCollection($this->getEmptyIteratorAggregate(), $logger))
            ->getEntityResource('FooBar');
    }

    public function testThatLoggerIsCalledIfGetEntityResourceMethodGetIteratorThrowsAnException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Resource class does not exist for entity \'FooBar\'');

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $logger
            ->expects(self::once())
            ->method('error');

        (new ResourceCollection($this->getIteratorAggregateThatThrowsAnException(), $logger))
            ->getEntityResource('FooBar');
    }

    public function testThatGetAllReturnsCorrectCountOfResources(): void
    {
        self::assertCount(9, $this->getCollection()->getAll());
    }

    public function testThatCountMethodReturnsExpectedCount(): void
    {
        self::assertSame(9, $this->getCollection()->count(), 'REST resource count from collection was not expected');
    }

    /**
     * @dataProvider dataProviderTestThatGetReturnsExpectedResource
     *
     * @param class-string<RestResource> $resourceClass
     *
     * @testdox Test that `get` method with `$resourceClass` input returns instance of that resource class.
     */
    public function testThatGetReturnsExpectedResource(string $resourceClass): void
    {
        self::assertInstanceOf($resourceClass, $this->getCollection()->get($resourceClass));
    }

    /**
     * @dataProvider dataProviderTestThatGetEntityResourceReturnsExpectedResource
     *
     * @param class-string<RestResource> $resourceClass
     * @param class-string<EntityInterface> $entityClass
     *
     * @testdox Test that `getEntityResource` method with `$entityClass` input returns `$resourceClass` class.
     */
    public function testThatGetEntityResourceReturnsExpectedResource(string $resourceClass, string $entityClass): void
    {
        /** @noinspection UnnecessaryAssertionInspection */
        self::assertInstanceOf($resourceClass, $this->getCollection()->getEntityResource($entityClass));
    }

    /**
     * @dataProvider dataProviderTestThatHasReturnsExpected
     *
     * @param class-string<RestResource>|string|null $resource
     *
     * @testdox Test that `has` method returns `$expected` with `$resource` input.
     */
    public function testThatHasReturnsExpected(bool $expected, ?string $resource): void
    {
        self::assertSame($expected, $this->getCollection()->has($resource));
    }

    /**
     * @dataProvider dataProviderTestThatHasEntityResourceReturnsExpected
     *
     * @param class-string<EntityInterface>|string|null $entity
     *
     * @testdox Test that `hasEntityResource` method returns `$expected` with `$entity` input.
     */
    public function testThatHasEntityResourceReturnsExpected(bool $expected, ?string $entity): void
    {
        self::assertSame($expected, $this->getCollection()->hasEntityResource($entity));
    }

    /**
     * @return Generator<array{0: class-string<RestResource>}>
     */
    public function dataProviderTestThatGetReturnsExpectedResource(): Generator
    {
        yield [ApiKeyResource::class];
        yield [DateDimensionResource::class];
        yield [HealthzResource::class];
        yield [LogLoginFailureResource::class];
        yield [LogLoginResource::class];
        yield [LogRequestResource::class];
        yield [RoleResource::class];
        yield [UserGroupResource::class];
        yield [UserResource::class];
    }

    /**
     * @return Generator<array{
     *      0: class-string<RestResource>,
     *      1: class-string<EntityInterface>
     *  }>
     */
    public function dataProviderTestThatGetEntityResourceReturnsExpectedResource(): Generator
    {
        yield [ApiKeyResource::class, ApiKey::class];
        yield [DateDimensionResource::class, DateDimension::class];
        yield [HealthzResource::class, Healthz::class];
        yield [LogLoginFailureResource::class, LogLoginFailure::class];
        yield [LogLoginResource::class, LogLogin::class];
        yield [LogRequestResource::class, LogRequest::class];
        yield [RoleResource::class, Role::class];
        yield [UserGroupResource::class, UserGroup::class];
        yield [UserResource::class, User::class];
    }

    /**
     * @return Generator<array{0: boolean, 1: class-string<RestResource>|string|null}>
     */
    public function dataProviderTestThatHasReturnsExpected(): Generator
    {
        yield [true, ApiKeyResource::class];
        yield [true, DateDimensionResource::class];
        yield [true, HealthzResource::class];
        yield [true, LogLoginFailureResource::class];
        yield [true, LogLoginResource::class];
        yield [true, LogRequestResource::class];
        yield [true, RoleResource::class];
        yield [true, UserGroupResource::class];
        yield [true, UserResource::class];
        yield [false, null];
        yield [false, 'ResourceThatDoesNotExists'];
        yield [false, stdClass::class];
    }

    /**
     * @return Generator<array{0: boolean, 1: class-string<EntityInterface>|string|null}>
     */
    public function dataProviderTestThatHasEntityResourceReturnsExpected(): Generator
    {
        yield [true, ApiKey::class];
        yield [true, DateDimension::class];
        yield [true, Healthz::class];
        yield [true, LogLoginFailure::class];
        yield [true, LogLogin::class];
        yield [true, LogRequest::class];
        yield [true, Role::class];
        yield [true, UserGroup::class];
        yield [true, User::class];
        yield [false, null];
        yield [false, 'ResourceThatDoesNotExists'];
        yield [false, stdClass::class];
    }

    private function getCollection(): ResourceCollection
    {
        $service = self::getContainer()->get(ResourceCollection::class);

        self::assertInstanceOf(ResourceCollection::class, $service);

        return $service;
    }

    /**
     * @return IteratorAggregate<mixed>
     */
    private function getEmptyIteratorAggregate(): IteratorAggregate
    {
        return new class([]) implements IteratorAggregate {
            /**
             * @phpstan-var ArrayObject<int, mixed>
             */
            private ArrayObject $iterator;

            /**
             * @param array<mixed> $input
             */
            public function __construct(array $input)
            {
                $this->iterator = new ArrayObject($input);
            }

            /**
             * @phpstan-return ArrayObject<int, mixed>
             */
            public function getIterator(): ArrayObject
            {
                return $this->iterator;
            }
        };
    }

    /**
     * @return IteratorAggregate<mixed>
     */
    private function getIteratorAggregateThatThrowsAnException(): IteratorAggregate
    {
        return new class() implements IteratorAggregate {
            /**
             * @phpstan-return ArrayObject<int, mixed>
             */
            public function getIterator(): ArrayObject
            {
                throw new LogicException('Exception with getIterator');
            }
        };
    }
}
