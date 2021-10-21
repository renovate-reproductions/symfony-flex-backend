<?php
declare(strict_types = 1);
/**
 * /tests/E2E/Controller/v1/Role/FindOneRoleControllerTest.php
 *
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@pinja.com>
 */

namespace App\Tests\E2E\Controller\v1\Role;

use App\Utils\Tests\WebTestCase;
use Generator;
use Throwable;

/**
 * Class FindOneRoleControllerTest
 *
 * @package App\Tests\E2E\Controller\v1\Role
 * @author TLe, Tarmo Leppänen <tarmo.leppanen@pinja.com>
 */
class FindOneRoleControllerTest extends WebTestCase
{
    private string $baseUrl = '/v1/role';

    /**
     * @throws Throwable
     *
     * @testdox Test that `GET /v1/role/ROLE_ADMIN` returns HTTP status `401` for non-logged in user
     */
    public function testThatFindOneRoleReturns401(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', $this->baseUrl . '/ROLE_ADMIN');

        $response = $client->getResponse();
        $content = $response->getContent();

        self::assertNotFalse($content);
        self::assertSame(401, $response->getStatusCode(), $content . "\nResponse:\n" . $response);
    }

    /**
     * @dataProvider dataProviderTestThatFindOneRoleReturns403
     *
     * @throws Throwable
     *
     * @testdox Test that `GET /v1/role/ROLE_ADMIN` returns HTTP status `403` when using `$u` + `$p` credentials
     */
    public function testThatFindOneRoleReturns403(string $u, string $p): void
    {
        $client = $this->getTestClient($u, $p);
        $client->request('GET', $this->baseUrl);

        $response = $client->getResponse();
        $content = $response->getContent();

        self::assertNotFalse($content);
        self::assertSame(403, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @dataProvider dataProviderTestThatFindOneActionWorksAsExpected
     *
     * @throws Throwable
     *
     * @testdox Test that `findOne` action returns HTTP status `200` with `$username` + `$password` credentials
     */
    public function testThatFindOneActionWorksAsExpected(string $username, string $password): void
    {
        $client = $this->getTestClient($username, $password);

        $client->request('GET', $this->baseUrl . '/ROLE_ADMIN');

        $response = $client->getResponse();
        $content = $response->getContent();

        self::assertNotFalse($content);
        self::assertSame(200, $response->getStatusCode(), $content . "\nResponse:\n" . $response);
    }

    /**
     * @dataProvider dataProviderTestThatFindOneActionReturns404
     *
     * @throws Throwable
     *
     * @testdox Test that `findOne` action returns HTTP status `404` with `$username` + `$password` credentials
     */
    public function testThatFindOneActionReturns404(string $username, string $password): void
    {
        $client = $this->getTestClient($username, $password);

        $client->request('GET', $this->baseUrl . '/ROLE_FOOBAR');

        $response = $client->getResponse();
        $content = $response->getContent();

        self::assertNotFalse($content);
        self::assertSame(404, $response->getStatusCode(), $content . "\nResponse:\n" . $response);
    }

    /**
     * @return Generator<array{0: string, 1: string}>
     */
    public function dataProviderTestThatFindOneRoleReturns403(): Generator
    {
        yield ['john', 'password'];
        yield ['john-api', 'password-api'];
        yield ['john-logged', 'password-logged'];
        yield ['john-user', 'password-user'];
        yield ['john.doe@test.com', 'password'];
        yield ['john.doe-api@test.com', 'password-api'];
        yield ['john.doe-logged@test.com', 'password-logged'];
        yield ['john.doe-user@test.com', 'password-user'];
    }

    /**
     * @return Generator<array{0: string, 1: string}>
     */
    public function dataProviderTestThatFindOneActionWorksAsExpected(): Generator
    {
        yield ['john-admin', 'password-admin'];
        yield ['john-root', 'password-root'];
        yield ['john.doe-admin@test.com', 'password-admin'];
        yield ['john.doe-root@test.com', 'password-root'];
    }

    /**
     * @return Generator<array{0: string, 1: string}>
     */
    public function dataProviderTestThatFindOneActionReturns404(): Generator
    {
        return $this->dataProviderTestThatFindOneActionWorksAsExpected();
    }
}
