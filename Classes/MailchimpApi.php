<?php
namespace Tev\TevMailchimp;

use Exception;
use DrewM\MailChimp\MailChimp;

/**
 * Simple Mailchimp API client.
 *
 * Uses: https://github.com/drewm/mailchimp-api
 */
class MailchimpApi
{
    /**
     * Mailchimp API.
     *
     * @var \DrewM\MailChimp\MailChimp
     */
    private $mc;

    /**
     * Constructor.
     *
     * @param  string $apiKey Mailchimp API key
     * @return void
     */
    public function __construct($apiKey)
    {
        $this->mc = new MailChimp($apiKey);
    }

    /**
     * Get all lists.
     *
     * @return array
     *
     * @throws \Exception On error
     */
    public function getLists()
    {
        return $this->request('get', 'lists', [
            'fields' => 'lists.id,lists.name,lists.date_created'
        ])['lists'];
    }

    /**
     * Get list member details.
     *
     * @param  string $listId   Mailchimp list ID
     * @param  string $memberId Mailchimp member ID (hashed)
     * @return array
     *
     * @throws \Exception On error
     */
    public function getMember($listId, $memberId)
    {
        return $this->request('get', "lists/{$listId}/members/$memberId");
    }

    /**
     * Add a member to the given list.
     *
     * @param  string $listId  Mailchimp list ID
     * @param  array  $details Subscriber details
     * @return array
     *
     * @throws \Exception On error
     */
    public function addMember($listId, $details)
    {
        return $this->request('post', "lists/{$listId}/members", $details);
    }

    /**
     * Update an existing member for the given list.
     *
     * @param  string $listId   Mailchimp list ID
     * @param  string $memberId Mailchimp member ID (hashed)
     * @param  array  $details  Subscriber details
     * @return array
     *
     * @throws \Exception On error
     */
    public function updateMember($listId, $memberId, $details)
    {
        return $this->request('patch', "lists/{$listId}/members/{$memberId}", $details);
    }

    /**
     * Make a request to the Mailchimp API.
     *
     * @param  string $method   Request method (get, post, patch etc)
     * @param  string $endpoint Endpoint URL (e.g 'lists')
     * @param  array  $args     Request arguments
     * @return array            Response data
     *
     * @throws \Exception On error
     */
    private function request($method, $endpoint, $args = [])
    {
        $data = $this->mc->$method($endpoint, $args, 5);
        $response = $this->mc->getLastResponse();

        if (isset($response['headers']['http_code'])) {
            switch (substr($response['headers']['http_code'], 0, 1)) {
                case '4':
                    throw new Exception($data['title'] . ': ' . $data['detail']);

                case '5':
                    throw new Exception('Mailchimp API 500 error. Service may be unavailable.');

                default:
                    return $data;
            }
        } else {
            throw new Exception('Unknown Mailchimp error: ' . $this->mc->getLastError());
        }
    }
}
