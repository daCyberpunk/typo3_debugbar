<?php namespace Konafets\Typo3Debugbar\DataCollectors;

use DebugBar\DataCollector\MessagesCollector;
use Konafets\Typo3Debugbar\AssetsRenderer;
use Konafets\Typo3Debugbar\Typo3DebugBar;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class VarDumpCollector
 *
 * @author Stefano Kowalke <info@arroba-it.de>
 */
class RequestCollector extends MessagesCollector
{

    /**
     * The constructor
     *
     * @param string $name
     */
    public function __construct($name = 'request')
    {
        parent::__construct($name);
    }

    /**
     * Returns the unique name of the collector
     *
     * @return string
     */
    public function getName()
    {
        return 'request';
    }

    public function addVarDump($item, $label = 'request')
    {
        $this->addMessage($item, $label);
    }

    public function getVarDumpItems()
    {
        return $this->getMessages();
    }

    /**
     * Returns a hash where keys are control names and their values
     * an array of options as defined in {@see DebugBar\JavascriptRenderer::addControl()}
     *
     * @return array
     */
    public function getWidgets()
    {
        $parentWidgets = parent::getWidgets();
        $name = $this->getName();
        $keys = [$name, $name . ':badge'];

        $widgets = \array_combine($keys, $parentWidgets);
        $widgets[$name]['widget'] = 'PhpDebugBar.Widgets.TYPO3GenericWidget';

        return $widgets;
    }

    /**
     * Returns an array with the following keys:
     *  - base_path
     *  - base_url
     *  - css: an array of filenames
     *  - js: an array of filenames
     *  - inline_css: an array map of content ID to inline CSS content (not including <style> tag)
     *  - inline_js: an array map of content ID to inline JS content (not including <script> tag)
     *  - inline_head: an array map of content ID to arbitrary inline HTML content (typically
     *        <style>/<script> tags); it must be embedded within the <head> element
     *
     * All keys are optional.
     *
     * Ideally, you should store static assets in filenames that are returned via the normal css/js
     * keys.  However, the inline asset elements are useful when integrating with 3rd-party
     * libraries that require static assets that are only available in an inline format.
     *
     * The inline content arrays require special string array keys:  the caller of this function
     * will use them to deduplicate content.  This is particularly useful if multiple instances of
     * the same asset provider are used.  Inline assets from all collectors are merged together into
     * the same array, so these content IDs effectively deduplicate the inline assets.
     *
     * @return array
     */
    public function getAssets()
    {
        $path = ExtensionManagementUtility::extPath(Typo3DebugBar::EXTENSION_KEY);

        return [
            'js' => $path . AssetsRenderer::PATH_TO_JAVASCRIPT . '/generic/widget.js',
        ];
    }
}
