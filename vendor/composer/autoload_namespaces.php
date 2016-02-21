<?php

// autoload_namespaces.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'ValueValidators\\' => array($vendorDir . '/data-values/interfaces/src'),
    'ValueParsers\\' => array($vendorDir . '/data-values/interfaces/src', $vendorDir . '/data-values/common/src'),
    'ValueFormatters\\' => array($vendorDir . '/data-values/interfaces/src', $vendorDir . '/data-values/common/src'),
    'Psr\\Log\\' => array($vendorDir . '/psr/log'),
    'Liuggio' => array($vendorDir . '/liuggio/statsd-php-client/src'),
    'Less' => array($vendorDir . '/oyejorge/less.php/lib'),
    'Kafka\\' => array($vendorDir . '/nmred/kafka-php/src'),
    'DataValues\\' => array($vendorDir . '/data-values/data-values/src', $vendorDir . '/data-values/common/src'),
    'Composer\\Installers\\' => array($vendorDir . '/composer/installers/src'),
    'ComposerHookHandler' => array($baseDir . '/includes/composer'),
    '' => array($vendorDir . '/cssjanus/cssjanus/src', $vendorDir . '/kzykhys/pygments/src'),
);