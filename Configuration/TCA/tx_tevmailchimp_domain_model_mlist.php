<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:tev_mailchimp/Resources/Private/Language/locallang_tca.xml:tx_tevmailchimp_domain_model_mlist',
        'label' => 'name',
        'crdate' => 'crdate',
        'tstamp' => 'tstamp',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'default_sortby' => 'ORDER BY name',
        'searchFields' => 'name, description, mc_list_id',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('tev_mailchimp') . 'ext_icon.png',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, name'
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;Basic Info,
                hidden,
                name,
                description,
                sorting,
                --div--;Subscribers,
                fe_users,
                --div--;Mailchimp Config,
                mc_list_id,
                mc_created_at
            '
        ]
    ],
    'columns' => [
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0'
            ]
        ],
        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:tev_mailchimp/Resources/Private/Language/locallang_tca.xml:tx_tevmailchimp_domain_model_mlist.name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'readOnly' => true
            ]
        ],
        'description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:tev_mailchimp/Resources/Private/Language/locallang_tca.xml:tx_tevmailchimp_domain_model_mlist.description',
            'config' => [
                'type' => 'text',
                'size' => '30',
                'eval' => 'trim'
            ]
        ],
        'mc_list_id' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:tev_mailchimp/Resources/Private/Language/locallang_tca.xml:tx_tevmailchimp_domain_model_mlist.mc_list_id',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'readOnly' => true
            ]
        ],
        'mc_created_at' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:tev_mailchimp/Resources/Private/Language/locallang_tca.xml:tx_tevmailchimp_domain_model_mlist.mc_created_at',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'datetime',
                'readOnly' => true
            ]
        ],
        'fe_users' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:tev_mailchimp/Resources/Private/Language/locallang_tca.xml:tx_tevmailchimp_domain_model_mlist.fe_users',
            'config' => [
                'type' => 'select',
                'readOnly' => true,
                'size' => '4',
                'maxitems' => 9999,
                'foreign_table' => 'fe_users',
                'MM' => 'tx_tevmailchimp_domain_model_mlist_fe_user_mm'
            ]
        ],
        'sorting' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:tev_mailchimp/Resources/Private/Language/locallang_tca.xml:tx_tevmailchimp_domain_model_mlist.sorting',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim,int'
            ]
        ]
    ]
];
