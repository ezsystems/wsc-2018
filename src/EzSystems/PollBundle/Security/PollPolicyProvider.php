<?php

namespace EzSystems\PollBundle\Security;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;


class PollPolicyProvider implements PolicyProviderInterface
{
    /**
     * @param \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface $configBuilder
     *
     * @return array|void
     */
    public function addPolicies(ConfigBuilderInterface $configBuilder)
    {
        $configBuilder->addConfig([
            'poll' => [
                'list' => null,
                'show' => ['Question'],
            ],
        ]);
    }
}