<?php
namespace Magenticians\Launcher\Block;

class LauncherItems extends \Magento\Backend\Block\Template
{
    const ITEMS_SEPARATOR = ' - ';

    protected $_template = 'launcher.phtml';

    /**
     * @var \Magento\Backend\Model\Menu\Filter\IteratorFactory
     */
    protected $_iteratorFactory;

    /**
     * @var \Magento\Backend\Block\Menu
     */
    protected $_blockMenu;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_url;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Menu\Filter\IteratorFactory $iteratorFactory
     * @param \Magento\Backend\Block\Menu $blockMenu
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Menu\Filter\IteratorFactory $iteratorFactory,
        \Magento\Backend\Block\Menu $blockMenu,
        \Magento\Backend\Model\UrlInterface $url,
        array $data
    ) {
        $this->_iteratorFactory = $iteratorFactory;
        $this->_blockMenu = $blockMenu;
        $this->_url = $url;
        parent::__construct($context, $data);
    }

    /**
     * Get menu config model
     *
     * @return \Magento\Backend\Model\Menu
     */
    public function getMenuModel()
    {
        return $this->_blockMenu->getMenuModel();
    }

    /**
     * Get menu filter iterator
     *
     * @param \Magento\Backend\Model\Menu $menu
     * @return \Magento\Backend\Model\Menu\Filter\Iterator
     */
    protected function getMenuIterator($menu)
    {
        return $this->_iteratorFactory->create(array('iterator' => $menu->getIterator()));
    }

    /**
     * Recursively iterate through the menu model
     * @param $menu
     * @param array $result
     * @param string $fullName
     * @return array
     */
    public function getMenuArray($menu, & $result = array(), $fullName = '')
    {
        if (! empty($fullName)) {
            $fullName .= self::ITEMS_SEPARATOR;
        }

        foreach ($this->getMenuIterator($menu) as $menuItem) {
            /** @var $menuItem \Magento\Backend\Model\Menu\Item  */

            if ($menuItem->getUrl() !== '#') {
                // Only add meaningful entries
                $result[] = array(
                    'value' => $menuItem->getUrl(),
                    'label' => $fullName . $menuItem->getTitle()
                );
            }

            if ($menuItem->hasChildren()) {
                $this->getMenuArray($menuItem->getChildren(), $result, $fullName . $menuItem->getTitle());
            }
        }

        return $result;
    }

    /**
     * Transform $this->getMenuArray() into a JSON array to be fed to ui.autocomplete
     * Applies a bit of trickery to parse the secret form keys properly
     * @return string
     */
    public function getMenuJson()
    {
        $menuArray = $this->getMenuArray($this->getMenuModel());
        return json_encode($menuArray, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Processing block html after rendering
     * @todo this is duplicate code
     * @see Magento\Backend\Block\Menu::_afterToHtml()
     *
     * @param   string $html
     * @return  string
     */
    public function _afterToHtml($html)
    {
        $html = preg_replace_callback(
            '#' . \Magento\Backend\Model\UrlInterface::SECRET_KEY_PARAM_NAME . '/\$([^\/].*)/([^\/].*)/([^\$].*)\$#U',
            array($this, '_callbackSecretKey'),
            $html
        );

        return $html;
    }

    /**
     * Replace Callback Secret Key
     * @todo this is duplicate code
     * @see Magento\Backend\Block\Menu::_callbackSecretKey
     *
     * @param string[] $match
     * @return string
     */
    protected function _callbackSecretKey($match)
    {
        return \Magento\Backend\Model\UrlInterface::SECRET_KEY_PARAM_NAME . '/' . $this->_url->getSecretKey(
            $match[1],
            $match[2],
            $match[3]
        );
    }
}