<?php
declare(strict_types = 1);
/**
 * /tests/Functional/Repository/LogRequestRepositoryTest.php
 *
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@pinja.com>
 */

namespace App\Tests\Functional\Repository;

use App\Repository\LogRequestRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Throwable;

/**
 * Class LogRequestRepositoryTest
 *
 * @package App\Tests\Functional\Repository
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@pinja.com>
 */
class LogRequestRepositoryTest extends KernelTestCase
{
    /**
     * @throws Throwable
     */
    public function testThatCleanHistoryReturnsExpected(): void
    {
        $repository = self::getContainer()->get(LogRequestRepository::class);

        self::assertInstanceOf(LogRequestRepository::class, $repository);
        self::assertSame(0, $repository->cleanHistory());
    }
}
