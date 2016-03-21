<?php

if (!defined ('TYPO3_MODE')) {
    die('Access denied.');
}

// Automatically include extension Typoscript

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/Configuration/Typoscript/constants.ts">'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/Configuration/Typoscript/setup.ts">'
);

// Register Mailchimp commands

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Tev\\TevMailchimp\\Command\\MailchimpCommandController';

// Configure the webhook plugin

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Tev.' . $_EXTKEY,
    'Webhooks',
    [
        'Webhook' => 'listen'
    ],
    [
        'Webhook' => 'listen'
    ]
);

// Configure logging

$tevMcConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tev_mailchimp']);

$GLOBALS['TYPO3_CONF_VARS']['LOG']['Tev']['TevMailchimp']['writerConfiguration'] = [
    \TYPO3\CMS\Core\Log\LogLevel::DEBUG => [
        'Tev\\Typo3Utils\\Log\\Writer\\FileWriter' => [
            'logFile' => $tevMcConf['logfile_path'] ?: 'typo3temp/logs/mailchimp.log'
        ]
    ]
];
