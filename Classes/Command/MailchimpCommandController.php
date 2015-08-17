<?php
namespace Tev\TevMailchimp\Command;

use Exception;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use Tev\TevMailchimp\Services\Mailchimp;

/**
 * Command controller for Mailchimp.
 */
class MailchimpCommandController extends CommandController
{
    /**
     * Mailchimp service.
     *
     * @var \Tev\TevMailchimp\Services\MailchimpService
     * @inject
     */
    protected $mailchimpService;

    /**
     * Download all lists from Mailchimp.
     *
     * @return void
     */
    public function listsCommand()
    {
        $this->outputLine('<info>Downloading lists...</info>');

        try {
            $lists = $this->mailchimpService->downloadLists();

            foreach ($lists as $l) {
                $this->outputLine("<comment>- {$l->getMcListId()} downloaded</comment>");
            }

            $this->outputLine('<info>complete</info>');
        } catch (Exception $e) {
            $this->outputLine("<error>Error: {$e->getMessage()}</error>");
        }
    }
}
