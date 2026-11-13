<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Twig\Environment;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        $content = $this->twig->render('security/access_denied.html.twig', [
            'message' => $accessDeniedException->getMessage(),
            'requested_path' => $request->getPathInfo(),
        ]);

        return new Response($content, Response::HTTP_FORBIDDEN);
    }
}
