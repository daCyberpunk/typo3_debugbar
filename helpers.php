<?php

use FalkRoeder\Falk\Utility\TypeHandling;
use Konafets\Typo3Debugbar\Typo3DebugBar;
use TYPO3\CMS\Core\Utility\GeneralUtility;

if (! function_exists('debugbar')) {
    /**
     * Get the Debugbar instance
     *
     * @return Typo3DebugBar
     */
    function debugbar()
    {
        return GeneralUtility::makeInstance(Typo3DebugBar::class);
    }
}

if (! function_exists('deb')) {
    /**
     * Get the Debugbar instance
     *
     * @return Typo3DebugBar
     * @throws \FalkRoeder\Falk\Configuration\InvalidConfigurationFileException
     */
    function deb($value)
    {
        $yamlConf = new \FalkRoeder\Falk\Configuration\YamlConfiguration('Debugbar', 'Debugbar');
        $configuration = $yamlConf('typo3_debugbar');

        $type = TypeHandling::isJson($value) ? 'json' : (\is_object($value) ? get_class($value) : 'php');

        if ($type === 'php' || $type === 'json') {
            call_user_func_array(
                [debugbar(), $configuration['Typemap'][$type] ?? 'var_dump'],
                \array_merge(func_get_args(), [$type])
            );
            return;
        }


        foreach ($configuration['Classmap'] as $funcName => $classmap) {
            if (!method_exists(debugbar(), $funcName)) {
                continue;
            }
            if (in_array($type, $classmap, true)) {
                $func = $funcName;
                break;
            }

            foreach ($classmap as $className) {
                if ($value instanceof $className) {
                    $func = $funcName;
                    break 2;
                }
            }
        }

        call_user_func_array([debugbar(), $func], func_get_args());
    }
}

if (! function_exists('debugbar_debug')) {
    /**
     * Adds one or more messages to the MessagesCollector
     *
     * @param mixed ...$value
     */
    function debugbar_debug($value)
    {
        $debugbar = debugbar();
        foreach (func_get_args() as $value) {
            $debugbar->addMessage($value, 'debug');
        }
    }
}

if (! function_exists('start_measure')) {
    /**
     * Starts a measure
     *
     * @param string $name Internal name, used to start the measure
     * @param string $label Public name
     */
    function start_measure($name, $label = null)
    {
        debugbar()->startMeasure($name, $label);
    }
}

if (! function_exists('stop_measure')) {
    /**
     * Stop a measure
     *
     * @param string $name Internal name, used to stop the measure
     */
    function stop_measure($name)
    {
        debugbar()->stopMeasure($name);
    }
}

if (! function_exists('add_measure')) {
    /**
     * Adds a measure
     *
     * @param string $label
     * @param float $start
     * @param float $end
     * @throws \DebugBar\DebugBarException
     */
    function add_measure($label, $start, $end)
    {
        debugbar()->addMeasure($label, $start, $end);
    }
}

if (! function_exists('measure')) {
    /**
     * Utility function to measure the execution of a Closure
     *
     * @param string $label
     * @param \Closure $closure
     * @throws \DebugBar\DebugBarException
     */
    function measure($label, \Closure $closure)
    {
        debugbar()->measure($label, $closure);
    }
}
