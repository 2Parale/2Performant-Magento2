<?php
use Magento\Framework\Component\ComponentRegistrar;

$registrar = new ComponentRegistrar();
if ($registrar->getPath(ComponentRegistrar::MODULE, 'TwoPerformant_Tracking') === null) {
    ComponentRegistrar::register(ComponentRegistrar::MODULE, 'TwoPerformant_Tracking', __DIR__);
}
