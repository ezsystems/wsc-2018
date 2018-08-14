<?php

namespace AppBundle;

use AppBundle\Security\PollPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
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
