<?php

namespace EzSystems\PollBundle;

use EzSystems\PollBundle\Security\PollPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzSystemsPollBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // Retrieve "ezpublish" container extension.
        /** @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension $eZExtension */
        $eZExtension = $container->getExtension('ezpublish');

        // Add the policy provider.
        $eZExtension->addPolicyProvider(new PollPolicyProvider());
    }
}
