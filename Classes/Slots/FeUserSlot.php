<?php
namespace Tev\TevMailchimp\Slots;

use Tev\Typo3Utils\Slots\EntitySlot;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;

/**
 * Slot for handling FE User signals.
 */
class FeUserSlot extends EntitySlot
{
    /**
     * Mailchimp service.
     *
     * @var \Tev\TevMailchimp\Services\MailchimpService
     * @inject
     */
    protected $mailchimp;

    /**
     * User email field utility.
     *
     * @var \Tev\TevMailchimp\Utility\EmailField
     * @inject
     */
    protected $emailUtil;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('TYPO3\\CMS\\Extbase\\Domain\\Model\\FrontendUser');
    }

    /**
     * When a user is created, download their Mailchimp subscriptions.
     *
     * @param  \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user
     * @return void
     */
    protected function created(FrontendUser $user)
    {
        $this->mailchimp->downloadSubscriptions($user);
    }

    /**
     * When a user's email address is updated, download their Mailchimp subscriptions.
     *
     * @param  \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user
     * @return void
     */
    protected function updated(FrontendUser $user)
    {
        if ($this->isDirty($user, $this->emailUtil->getFieldNameCamel())) {
            $this->mailchimp->downloadSubscriptions($user);
        }
    }
}
