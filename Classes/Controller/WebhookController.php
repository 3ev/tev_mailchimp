<?php
namespace Tev\TevMailchimp\Controller;

use Exception;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller for handling Mailchimp webhooks.
 */
class WebhookController extends ActionController
{
    /**
     * Webhook factory.
     *
     * @var \Tev\TevMailchimp\Webhook\WebhookFactory
     * @inject
     */
    protected $webhookFactory;

    /**
     * Webhook service.
     *
     * @var \Tev\TevMailchimp\Services\WebhookService
     * @inject
     */
    protected $hookService;

    /**
     * Listen to an incoming webhook from Mailchimp.
     *
     * @return string
     */
    public function listenAction()
    {
        try {
            $this->hookService->process($this->webhookFactory->create($_REQUEST));

            return json_encode(['state' => 'success']);
        } catch (Exception $e) {
            $this->throwStatus(400, 'Bad Request', json_encode([
                'state' => 'error',
                'message' => $e->getMessage()
            ]));
        }
    }
}
