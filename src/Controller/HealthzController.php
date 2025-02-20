<?php
declare(strict_types = 1);
/**
 * /src/Controller/HealthzController.php
 *
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@pinja.com>
 */

namespace App\Controller;

use App\Rest\Interfaces\ResponseHandlerInterface;
use App\Rest\ResponseHandler;
use App\Utils\HealthzService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Class HealthzController
 *
 * @package App\Controller
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@pinja.com>
 */
class HealthzController
{
    public function __construct(
        private readonly ResponseHandler $responseHandler,
        private readonly HealthzService $healthzService,
    ) {
    }

    /**
     * Route for application health check. This action will make some simple
     * tasks to ensure that application is up and running like expected.
     *
     * @see https://kubernetes.io/docs/tasks/configure-pod-container/configure-liveness-readiness-probes/
     *
     * @OA\Get(
     *     operationId="healthz",
     *     responses={
     *          @OA\Response(
     *              response=200,
     *              description="success",
     *              @OA\Schema(
     *                  type="object",
     *                  example={"timestamp": "2018-01-01T13:08:05+00:00"},
     *                  @OA\Property(property="timestamp", type="string"),
     *              ),
     *          ),
     *     },
     *  )
     *
     * @throws Throwable
     */
    #[Route(
        path: '/healthz',
        methods: [Request::METHOD_GET],
    )]
    public function __invoke(Request $request): Response
    {
        return $this->responseHandler->createResponse(
            $request,
            $this->healthzService->check(),
            format: ResponseHandlerInterface::FORMAT_JSON,
            context: [
                'groups' => [
                    'Healthz.timestamp',
                ],
            ],
        );
    }
}
