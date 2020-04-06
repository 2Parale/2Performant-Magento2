<?php
use Magento\Framework\Component\ComponentRegistrar;

$registrar = new ComponentRegistrar();
if ($registrar->getPath(ComponentRegistrar::MODULE, 'TwoPerformant_Feed') === null) {
    ComponentRegistrar::register(ComponentRegistrar::MODULE, 'TwoPerformant_Feed', __DIR__);
}
