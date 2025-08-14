<?php

declare(strict_types=1);

namespace App\Controller\Application;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\LanguageService;

#[Route('/language', name: 'language_')]
class LanguageController extends AbstractController
{
    #[Route('/change-language/{code}', name: 'change_language', methods: ['GET'])]
    public function changeLanguage(string $code, Request $request, LanguageService $languageService): Response
    {
        $languages = $languageService->getLanguages();

        if (!(array_key_exists($code, $languages))) {
            $this->addFlash('error', $this->trans('application.language.language_change_error', [], 'application'));

            return $this->safeRedirect($request, 'app_index');
        }

        $session = $request->getSession();
        $session->set('_locale', $code);

        $this->addFlash('success', $this->trans('application.language.language_change_success', [], 'application'));

        return $this->safeRedirect($request, 'app_index');
    }

    #[Route('/get-languages', name: 'get_languages', methods: ['GET'])]
    public function getLanguages(LanguageService $languageService): JsonResponse
    {
        return new JsonResponse($languageService->getLanguages(), 200);
    }
}
