<?php

if (!defined ('TYPO3_MODE')) {
    die('Access denied.');
}

// BE save hooks

\Tev\Typo3Utils\Hook\EntityRegistrar::register('Tev\\TevMailchimp\\Hook\\FeUserHook');

// Configure the webhook plugin

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Tev.' . $_EXTKEY,
    'Webhooks',
    'Mailchimp Webhook Listener Plugin',
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('tev_mailchimp') . 'ext_icon.png'
);

// Register the wizard icon

if (TYPO3_MODE === 'BE') {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['Tev\\TevMailchimp\\Utility\\WizIcon'] =
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Utility/WizIcon.php';
}
