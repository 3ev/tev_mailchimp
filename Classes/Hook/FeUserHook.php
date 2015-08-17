<?php
namespace Tev\TevMailchimp\Hook;

use Tev\Typo3Utils\Hook\EntityHook;

/**
 * FE User BE hooks.
 */
class FeUserHook extends EntityHook
{
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('fe_users');
    }

    /**
     * {@inheritdoc}
     */
    protected function created($uid, $fields)
    {
        // If user is enabled, download their newsletters

        if (isset($fields['disable'])) {
            if (!$fields['disable']) {
                $this->downloadSubscriptions($uid);
            }
        } else {
            $this->downloadSubscriptions($uid);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function updated($uid, $dirty)
    {
        $emailUtil = $this->om->get('Tev\\TevMailchimp\\Utility\\EmailField');

        // User has been enabled, so download their newsletters

        if (isset($dirty['disable']) && !$dirty['disable']) {
            $this->downloadSubscriptions($uid);
        }

        // Email address has changed, so download subscriptions

        elseif (isset($dirty[$emailUtil->getFieldName()])) {
            $this->downloadSubscriptions($uid);
        }
    }

    /**
     * Download Mailchimp subscriptions for the given user.
     *
     * @param  int  $uid
     * @return void
     */
    private function downloadSubscriptions($uid)
    {
        $userRepo = $this->om->get('TYPO3\\CMS\\Extbase\\Domain\\Repository\\FrontendUserRepository');
        $mailchimp = $this->om->get('Tev\\TevMailchimp\\Services\\MailchimpService');

        if ($user = $userRepo->findByUid($uid)) {
            $mailchimp->downloadSubscriptions($user);
        }
    }
}
