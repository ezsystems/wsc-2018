<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace AppBundle\EventListener;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use JMS\TranslationBundle\Model\Message;

class MainMenuListener implements EventSubscriberInterface, TranslationContainerInterface
{
    public const ITEM_POLL_LIST = 'main__poll__list';

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [ConfigureMenuEvent::MAIN_MENU => 'onMainMenuConfigure'];
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent $event
     */
    public function onMainMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $menu->addChild(
            self::ITEM_POLL_LIST,
            [
                'route' => 'ez_systems_poll_list',
                'extras' => ['translation_domain' => 'menu'],
            ]
        );
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_POLL_LIST, 'menu'))->setDesc('Polls'),
        ];
    }
}
