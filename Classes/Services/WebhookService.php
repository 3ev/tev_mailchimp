<?php
namespace Tev\TevMailchimp\Services;

use TYPO3\CMS\Core\Log\LogManager;
use Tev\TevMailchimp\Webhook\Webhook;

/**
 * Mailchimp webhook service.
 *
 * Processes incoming webhooks.
 */
class WebhookService
{
    /**
     * Mailchimp list repository.
     *
     * @var \Tev\TevMailchimp\Domain\Repository\MlistRepository
     * @inject
     */
    protected $mListRepo;

    /**
     * FE user repository.
     *
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     * @inject
     */
    protected $feUserRepo;

    /**
     * User email field utility.
     *
     * @var \Tev\TevMailchimp\Utility\EmailField
     * @inject
     */
    protected $emailUtil;

    /**
     * Persistence manager.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $pm;

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
     * Process the given webhook.
     *
     * @param  \Tev\TevMailchimp\Webhook\Webhook $hook Incoming webhook
     * @return void
     */
    public function process(Webhook $hook)
    {
        switch ($hook->getType()) {
            case 'subscribe':
                $this->processSubscribe($hook);
                break;

            case 'unsubscribe':
                $this->processUnsubscribe($hook);
                break;

            case 'upemail':
                $this->processEmailChange($hook);
                break;

            default:
                break;
        }
    }

    /**
     * Process a subscribe webhook.
     *
     * @param  \Tev\TevMailchimp\Webhook\Webhook $hook Incoming webhook
     * @return void
     */
    private function processSubscribe(Webhook $hook)
    {
        $this->subscribe($hook->getData('email'), $hook->getData('list_id'));

        $this->logger->info('Processed subscribe hook', [
            'email' => $hook->getData('email'),
            'list_id' => $hook->getData('list_id')
        ]);
    }

    /**
     * Process an unsubscribe webhook.
     *
     * @param  \Tev\TevMailchimp\Webhook\Webhook $hook Incoming webhook
     * @return void
     */
    private function processUnsubscribe(Webhook $hook)
    {
        $this->unsubscribe($hook->getData('email'), $hook->getData('list_id'));

        $this->logger->info('Processed unsubscribe hook', [
            'email' => $hook->getData('email'),
            'list_id' => $hook->getData('list_id')
        ]);
    }

    /**
     * Process an email change webhook.
     *
     * @param  \Tev\TevMailchimp\Webhook\Webhook $hook Incoming webhook
     * @return void
     */
    private function processEmailChange(Webhook $hook)
    {
        $this->unsubscribe($hook->getData('old_email'), $hook->getData('list_id'));

        $this->subscribe($hook->getData('new_email'), $hook->getData('list_id'));

        $this->logger->info('Processed email change hook', [
            'old_email' => $hook->getData('old_email'),
            'new_email' => $hook->getData('new_email'),
            'list_id' => $hook->getData('list_id')
        ]);
    }

    /**
     * Handle a successful subscription by updating the local database.
     *
     * @param  string $email    Email address
     * @param  string $mcListId Mailchimp list ID (remote)
     * @return void
     */
    private function subscribe($email, $mcListId)
    {
        $method = 'findOneBy' . $this->emailUtil->getFieldNameUpperCamel();
        $list = $this->mListRepo->findOneByMcListId($mcListId);
        $user = $this->feUserRepo->{$method}($email);

        if ($list && $user) {
            $list->addFeUser($user);
            $this->mListRepo->update($list);
            $this->pm->persistAll();
        }
    }

    /**
     * Handle a successful unsubscription by updating the local database.
     *
     * @param  string $email    Email address that was unsubscribed
     * @param  string $mcListId Mailchimp list ID (remote)
     * @return void
     */
    private function unsubscribe($email, $mcListId)
    {
        $method = 'findOneBy' . $this->emailUtil->getFieldNameUpperCamel();
        $list = $this->mListRepo->findOneByMcListId($mcListId);
        $user = $this->feUserRepo->{$method}($email);

        if ($list && $user) {
            $list->removeFeUser($user);
            $this->mListRepo->update($list);
            $this->pm->persistAll();
        }
    }
}
