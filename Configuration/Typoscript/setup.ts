#
# Plugin (FE) config
#

plugin.tx_tevmailchimp {

    persistence {

        storagePid = {$plugin.tx_tevmailchimp.persistence.storagePid}

        classes {

            Tev\TevMailchimp\Domain\Model\Mlist {

                newRecordStoragePid = {$plugin.tx_tevmailchimp.persistence.storagePid}
            }
        }
    }
}

#
# Module (BE and CLI) config
#

module.tx_tevmailchimp < plugin.tx_tevmailchimp

#
# JSON page type for incoming Mailchimp webhooks.
#

tev_mailchimp_webhook_json = PAGE
tev_mailchimp_webhook_json {
    config {
        disableAllHeaderCode = 1
        additionalHeaders = Content-type:application/json
        xhtml_cleaning = 0
        admPanel = 0
        debug = 0
    }

    10 = USER
    10 {
        userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
        vendorName = Tev
        extensionName = TevMailchimp
        pluginName = Webhooks
    }
}
