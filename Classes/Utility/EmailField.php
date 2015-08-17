<?php
namespace Tev\TevMailchimp\Utility;

use TYPO3\CMS\Core\SingletonInterface;
use Stringy\Stringy as S;

/**
 * Utility for fetching the configured FE User email field in various different
 * formats.
 */
class EmailField implements SingletonInterface
{
    /**
     * Basic field name (underscored).
     *
     * @var string
     */
    private $fieldName;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tev_mailchimp']);

        $this->fieldName = $extConf['fe_user_email_field'];
    }

    /**
     * Get the field name in underscore format (field_name).
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * Get the field name in camel case (fieldName).
     *
     * @return string
     */
    public function getFieldNameCamel()
    {
        return S::create($this->fieldName)->camelize();
    }

    /**
     * Get the field name in upper camel case (FieldName).
     *
     * @return string
     */
    public function getFieldNameUpperCamel()
    {
        return S::create($this->fieldName)->upperCamelize();
    }
}
