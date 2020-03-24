<?php
namespace Tev\TevMailchimp\Command;

use Exception;
use TYPO3\CMS\Core\Log\LogManager;
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
     * Logger instance.
     *
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    /**
     * @param  \TYPO3\CMS\Core\Log\LogManager $logManager
     * @return void
     */
    public function injectLogManager(LogManager $logManager)
    {
        $this->logger = $logManager->getLogger(__CLASS__);
    }

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

            $this->logger->info('Lists downloaded successfully via CLI');
        } catch (Exception $e) {
            $this->outputLine("<error>Error: {$e->getMessage()}</error>");

            $this->logger->error('Lists failed to download via CLI', [
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Download all subscriptions from Mailchimp.
     *
     * @return void
     */
    public function subscriptionsCommand()
    {
        $this->outputLine('<info>Downloading subscriptions...</info>');

        try {
            $this->mailchimpService->downloadAllSubscriptions();

            $this->outputLine('<info>complete</info>');

            $this->logger->info('Subscriptions downloaded successfully via CLI');
        } catch (Exception $e) {
            $this->outputLine("<error>Error: {$e->getMessage()}</error>");

            $this->logger->error('Subscriptions failed to download via CLI', [
                'message' => $e->getMessage()
            ]);
        }
    }
}
