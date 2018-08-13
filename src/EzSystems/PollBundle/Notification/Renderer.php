<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PollBundle\Notification;

use eZ\Publish\API\Repository\Values\Notification\Notification;
use eZ\Publish\Core\Notification\Renderer\NotificationRenderer;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class Renderer implements NotificationRenderer
{
    /** @var \Twig\Environment */
    private $twig;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Routing\RouterInterface $router
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router
    ) {
        $this->twig = $twig;
        $this->router = $router;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Notification\Notification $notification
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(Notification $notification): string
    {
        if (!array_key_exists('fieldId', $notification->data)) {
            return '';
        }

        return $this->twig->render(
            'EzSystemsPollBundle:notification:notification_row.html.twig',
            [
                'notification' => $notification,
            ]
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Notification\Notification $notification
     *
     * @return string|null
     */
    public function generateUrl(Notification $notification): ?string
    {
        if (!array_key_exists('fieldId', $notification->data)) {
            return null;
        }

        return $this->router->generate('ez_systems_poll_show', [
            'fieldId' => $notification->data['fieldId'],
        ]);
    }
}
