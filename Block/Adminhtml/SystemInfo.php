<?php

namespace MatusStafura\SystemInfo\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use MatusStafura\SystemInfo\Helper\Info;

class SystemInfo extends Field
{
    protected $_helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Info $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $info = $this->_helper->getSystemInfoArray(); // use array, not string
        $html = '<table class="admin__table-secondary" style="width:100%;">';

        foreach ($info as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            if (strlen($value) > 150) {
                $value = '<pre style="white-space:pre-wrap;">' . htmlentities($value) . '</pre>';
            } else {
                $value = htmlentities($value);
            }

            $html .= "<tr><th style='text-align:left; padding:5px 10px;'>$key</th><td style='padding:5px 10px;'>$value</td></tr>";
        }

        $html .= '</table>';
        return $html;
    }

}
