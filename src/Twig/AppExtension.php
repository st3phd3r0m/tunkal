<?php

namespace App\Twig;

use App\Entity\Links;
use App\Repository\LinksRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private LinksRepository $linksRepository;

    public function getFunctions()
    {
        return [
            new TwigFunction('links', [$this, 'findLinks']),
        ];
    }

    public function __construct(LinksRepository $linksRepository)
    {
        $this->linksRepository = $linksRepository;
    }

    /**
     * Undocumented function
     *
     * @return Links[]
     */
    public function findLinks()
    {
        return $this->linksRepository->findActiveLinks();
    }
}
