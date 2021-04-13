<?php

namespace App\Twig;

use App\Repository\LinksRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('links', [$this, 'findLinks']),
        ];
    }

    protected $linksRepository;

    public function __construct(LinksRepository $linksRepository)
    {
        $this->linksRepository = $linksRepository;
    }

    public function findLinks()
    {
        return $this->linksRepository->findActiveLinks();
    }
}
