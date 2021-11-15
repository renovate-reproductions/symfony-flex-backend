<?php
declare(strict_types = 1);
/**
 * /tests/Integration/Controller/HealthzControllerTest.php
 *
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@pinja.com>
 */

namespace App\Tests\Integration\Controller;

use App\Controller\HealthzController;
use App\Entity\Healthz;
use App\Rest\Interfaces\ResponseHandlerInterface;
use App\Rest\ResponseHandler;
use App\Utils\HealthzService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

/**
 * Class HealthzControllerTest
 *
 * @package App\Tests\Integration\Controller
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@pinja.com>
 */
class HealthzControllerTest extends KernelTestCase
{
    /**
     * @throws Throwable
     *
     * @testdox Test that `__invoke` method calls expected service methods
     */
    public function testThatInvokeMethodIsCallingExpectedMethods(): void
    {
        $request = Request::create('/healthz');
        $healthz = new Healthz();

        $responseHandler = $this->getMockBuilder(ResponseHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $healthzService = $this->getMockBuilder(HealthzService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $healthzService
            ->expects(self::once())
            ->method('check')
            ->willReturn($healthz);

        $responseHandler
            ->expects(self::once())
            ->method('createResponse')
            ->with(
                $request,
                $healthz,
                null,
                null,
                ResponseHandlerInterface::FORMAT_JSON,
                [
                    'groups' => [
                        'Healthz.timestamp',
                    ],
                ],
            )
            ->willReturn(
                new JsonResponse(
                    [
                        'timestamp' => $healthz->getTimestamp(),
                    ],
                ),
            );

        $response = (new HealthzController($responseHandler, $healthzService))($request);
        $content = $response->getContent();

        self::assertSame(200, $response->getStatusCode());
        self::assertNotFalse($content);
        self::assertJson($content);
    }
}
