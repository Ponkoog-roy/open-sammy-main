<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Yaml;

class LanguageService
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getLanguages(): array
    {
        $configFile = $this->parameterBag->get('kernel.project_dir') . '/config/languages.yaml';

        return Yaml::parseFile($configFile);
    }
}