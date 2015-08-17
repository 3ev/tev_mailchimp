#
# Table structure for table 'tx_tevmailchimp_domain_model_mlist'
#

CREATE TABLE tx_tevmailchimp_domain_model_mlist (
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
    name varchar(255) DEFAULT '' NOT NULL,
    description varchar(255) DEFAULT '' NOT NULL,
    mc_list_id varchar(20) DEFAULT '' NOT NULL,
    mc_created_at int(11) unsigned DEFAULT '0' NOT NULL,
    fe_users int(11) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY deleted (deleted),
    KEY hidden (hidden),
    KEY mc_list_id (mc_list_id)
);

#
# Table structure for table 'tx_tevmailchimp_domain_model_mlist_fe_user_mm'
#

CREATE TABLE tx_tevmailchimp_domain_model_mlist_fe_user_mm (
    uid_local int(11) unsigned DEFAULT '0' NOT NULL,
    uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
    sorting int(11) DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);
