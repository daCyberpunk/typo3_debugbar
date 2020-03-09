<?php namespace Konafets\Typo3Debugbar\DataCollectors;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Class SessionCollector
 */
class SessionCollector extends DataCollector implements DataCollectorInterface, Renderable
{

    /**
     * Called by the DebugBar when data needs to be collected
     *
     * @return array Collected data
     */
    public function collect()
    {
        //return [];
        $sessionData =  $this->getSession();
        if (isset($sessionData['FE']['ses_data'])) {
            $sessionData['FE']['ses_data'] = \unserialize($sessionData['FE']['ses_data']);
        }

        if (isset($sessionData['BE']['ses_data'])) {
            $sessionData['BE']['ses_data'] = \unserialize($sessionData['BE']['ses_data']);
        }
        if (isset($sessionData['BE']['uc'])) {
            $sessionData['BE']['uc'] = \unserialize($sessionData['BE']['uc']);
        }


        ArrayUtility::removeNullValuesRecursive($sessionData);

        return $sessionData;

    }

    /**
     * Returns the unique name of the collector
     *
     * @return string
     */
    public function getName()
    {
        return 'session';
    }

    /**
     * Returns a hash where keys are control names and their values
     * an array of options as defined in {@see DebugBar\JavascriptRenderer::addControl()}
     *
     * @return array
     */
    public function getWidgets()
    {
        $name = $this->getName();

        return [
            "$name" => [
                'icon' => 'archive',
                'widget' => 'PhpDebugBar.Widgets.SessionWidget',
                'map' => 'session',
                'default' => '[]',
            ],
        ];
    }

    /**
     * @return mixed
     */
    private function getSession()
    {
        if (TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_FE) {
            //return $GLOBALS['TSFE']->fe_user->getSession();
            return [
                'FE' => $GLOBALS['TSFE']->fe_user->fetchUserSession(),
                'BE' => $GLOBALS['BE_USER']->fetchUserSession()
            ];
        }
        if (TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_BE) {
            return [
                'BE' => $GLOBALS['BE_USER']->fetchUserSession()
            ];
        }
    }
}
