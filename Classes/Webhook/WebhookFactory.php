<?php
namespace Tev\TevMailchimp\Webhook;

use Exception;
use InvalidArgumentException;
use Carbon\Carbon;
use TYPO3\CMS\Core\Log\LogManager;

/**
 * Instantiate webhook objects.
 */
class WebhookFactory
{
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
     * Create a webhook from request data.
     *
     * https://apidocs.mailchimp.com/webhooks/
     *
     * @param  array                             $data Request data
     * @return \Tev\TevMailchimp\Webhook\Webhook
     *
     * @throws \Exception If failed to instantiate webhook due to invalid data
     */
    public function create($data)
    {
        try {
            return new Webhook(
                $this->getType($data),
                $this->getFiredAt($data),
                $this->getData($data),
                $this->getMergeFields($data)
            );
        } catch (Exception $e) {
            $this->logger->error('Hook parsing failed', [
                'error' => $e->getMessage(),
                'hook_data' => $data
            ]);

            throw $e;
        }
    }

    /**
     * Get the webhook type from the request data.
     *
     * @param  array  $data
     * @return string
     *
     * @throws \Exception
     */
    private function getType($data)
    {
        if (isset($data['type'])) {
            return $data['type'];
        } else {
            throw new Exception('No \'type\' field supplied');
        }
    }

    /**
     * Get the fired at data from the request data.
     *
     * @param  array          $data
     * @return \Carbon\Carbon
     *
     * @throws \Exception
     */
    private function getFiredAt($data)
    {
        if (isset($data['fired_at'])) {
            try {
                return Carbon::createFromFormat('Y-m-d H:i:s', $data['fired_at']);
            } catch (InvalidArgumentException $e) {
                throw new Exception('\'fired_at\' field is not in \'Y-m-d H:i:s\' format');
            }
        } else {
            throw new Exception('No \'fired_at\' field supplied');
        }
    }

    /**
     * Get the data fields from the request data.
     *
     * @param  array $data
     * @return array
     *
     * @throws \Exception
     */
    private function getData($data)
    {
        if (isset($data['data'])) {
            $m = $data['data'];
            unset($m['merges']);
            return $m;
        } else {
            throw new Exception('No \'data\' field supplied');
        }
    }

    /**
     * Get the merge fields from the request data.
     *
     * @param  array $data
     * @return array
     *
     * @throws \Exception
     */
    private function getMergeFields($data)
    {
        if (isset($data['data'])) {
            $m = $data['data'];

            if (isset($m['merges'])) {
                return $m['merges'];
            } else {
                return [];
            }
        } else {
            throw new Exception('No \'data\' field supplied');
        }
    }
}
