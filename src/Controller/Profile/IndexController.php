<?php
declare(strict_types = 1);
/**
 * /src/Controller/Profile/IndexController.php
 *
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@pinja.com>
 */

namespace App\Controller\Profile;

use App\Entity\User;
use App\Security\RolesService;
use App\Utils\JSON;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class IndexController
 *
 * @package App\Controller\Profile
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@pinja.com>
 */
#[Route(
    path: '/profile',
    methods: [Request::METHOD_GET],
)]
#[Security('is_granted("IS_AUTHENTICATED_FULLY")')]
class IndexController
{
    public function __construct(
        private SerializerInterface $serializer,
        private RolesService $rolesService,
    ) {
    }

    /**
     * Endpoint action to get current user profile data.
     *
     * @OA\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      description="Authorization header",
     *      @OA\Schema(
     *          type="string",
     *          default="Bearer _your_jwt_here_",
     *      ),
     *  )
     * @OA\Response(
     *      response=200,
     *      description="User profile data",
     *      @OA\Schema(
     *          ref=@Model(
     *              type=User::class,
     *              groups={"set.UserProfile"},
     *          ),
     *      ),
     *  )
     * @OA\Response(
     *      response=401,
     *      description="Invalid token",
     *      @OA\Schema(
     *          type="object",
     *          example={
     *              "Token not found": "{code: 401, message: 'JWT Token not found'}",
     *              "Expired token": "{code: 401, message: 'Expired JWT Token'}",
     *          },
     *          @OA\Property(property="code", type="integer", description="Error code"),
     *          @OA\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     * @OA\Tag(name="Profile")
     *
     * @throws JsonException
     */
    public function __invoke(User $loggedInUser): JsonResponse
    {
        /** @var array<string, string|array<string, string>> $output */
        $output = JSON::decode(
            $this->serializer->serialize($loggedInUser, 'json', ['groups' => 'set.UserProfile']),
            true
        );

        /** @var array<int, string> $roles */
        $roles = $output['roles'];

        $output['roles'] = $this->rolesService->getInheritedRoles($roles);

        return new JsonResponse($output);
    }
}
