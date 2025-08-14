<?php
declare(strict_types=1);

namespace App\Tests\functional;

use App\Entity\User;
use App\Enum\Role;
use App\Tests\_support\AbstractWebTestCase;
use App\Tests\builders\UserBuilder;
use Symfony\Component\HttpFoundation\Request;

class LanguageControllerTest extends AbstractWebTestCase
{
    /**
     * @dataProvider changeLanguageProvider
     * @testdox $_dataName
     */
    public function testChangeLanguage(array $entitiesToPersist, User $user, string $languageCodeToChangeTo, array $expectedText): void
    {
        $this->persistEntities(...$entitiesToPersist);

        $this->client->loginUser($user, "boardworks");

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_language_change_language', ['code' => $languageCodeToChangeTo]));
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_dashboard_index'));

        self::assertSelectorExists('#language-dropdown');

        $crawler = $this->client->getCrawler();

        $result = [
            "Dashboard" => $crawler->filter('li.nav-item a[href="/dashboard"] span')->text(),
            "Assessment" => $crawler->filter('li.nav-item a[href="/model/showPractice"] span')->text(),
            "Reporting" => $crawler->filter('li.nav-item a[href="/reporting"] span')->text(),
        ];

        foreach ($result as $key => $value) {
            self::assertEquals($expectedText[$key], $value);
        }
    }

    public function changeLanguageProvider(): \Generator
    {
        yield "Test 1 - Test that switching to spanish changes the translations" => [
            [
                $user = (new UserBuilder())->build(),
            ],
            $user,
            'es', // language code to change to
            [
                "Dashboard" => "Panel de control",
                "Assessment" => "Evaluación",
                "Reporting" => "Informes"
            ], // expected spanish translations
        ];
    }
}
