<?php


namespace Webgriffe\ConfigFileReader\Block\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field as BaseField;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Webgriffe\ConfigFileReader\Model\Config\DefaultYamlFile;

class Field extends BaseField
{
    const ACTION_SCOPE_TYPE = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT;

    /**
     * @var DefaultYamlFile
     */
    private $defaultYamlFile;

    public function __construct(Context $context, DefaultYamlFile $defaultYamlFile, array $data = [])
    {
        parent::__construct($context, $data);
        $this->defaultYamlFile = $defaultYamlFile;
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        if ($element->getData('scope') === self::ACTION_SCOPE_TYPE) {
            if ($this->isElementValueOverridden($element)) {
                $element->setData('disabled', true);
                $element->setComment(
                    sprintf(
                        '<strong><em>The value for this configuration setting, for scope "%s", comes from file ' .
                        'and cannot be modified.</em></strong><br/>',
                        self::ACTION_SCOPE_TYPE
                    ) .
                    $element->getComment()
                );
            }
        }
        return parent::_getElementHtml($element);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function isElementValueOverridden(AbstractElement $element)
    {
        $fieldConfig = $element->getData('field_config');
        $path = $fieldConfig['path'] . '/' . $fieldConfig['id'];
        $flattenOverridenValues = $this->defaultYamlFile->asFlattenArray();
        return array_key_exists($path, $flattenOverridenValues);
    }
}
