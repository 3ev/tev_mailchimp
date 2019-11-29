<?php
namespace Tev\TevMailchimp\Services;

use Exception;
use Carbon\Carbon;
use Tev\TevMailchimp\MailchimpApi;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use Tev\TevMailchimp\Domain\Model\Mlist;
use Tev\TevMailchimp\Domain\Repository\MlistRepository;

/**
 * Mailchimp service.
 *
 * Provides all of the methods needed by extension to interact with Mailchimp.
 */
class MailchimpService
{
    /**
     * Mailchimp API client.
     *
     * @var \Tev\TevMailchimp\MailchimpApi
     */
    protected $mc;

    /**
     * Mailchimp list repository.
     *
     * @var \Tev\TevMailchimp\Domain\Repository\MlistRepository
     * @inject
     */
    protected $mListRepo;

    /**
     * User repository.
     *
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     * @inject
     */
    protected $userRepo;

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
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tev_mailchimp']);

        $this->mc = new MailchimpApi($extConf['mc_api_key']);
    }

    /**
     * Download all lists from Mailchimp and store them in the database.
     *
     * Will soft delete any lists that no longer exist in Mailchimp.
     *
     * @return array All lists in the database
     *
     * @throws \Exception On Mailchimp API error
     */
    public function downloadLists()
    {
        try {
            $lists = $this->mc->getLists();

            $saved = [];

            foreach ($lists as $list) {
                $this->mListRepo->addOrUpdateFromMailchimp($list['id'], [
                    'name' => $list['name'],
                    'mc_created_at' => Carbon::parse($list['date_created'])->timestamp
                ]);

                $saved[] = $list['id'];
            };

            $this->mListRepo->deleteAllNotInList($saved);

            $this->pm->persistAll();

            return $this->mListRepo->findAll()->toArray();
        } catch (Exception $e) {
            $this->logger->error('MC API exception during downloadLists', [
                'message' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Download the lists the user is subscribed to.
     *
     * @param  \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user    User to get subscriptions for
     * @param  boolean                                      $newUser Optional. If true, this method will do less work on the database
     * @return array                                                 All lists the user is subscribed to
     */
    public function downloadSubscriptions(FrontendUser $user, $newUser = false)
    {
        $user = $this->castUser($user);

        $mcId = $this->getMailchimpId($this->getEmailFromUser($user));

        $subscribed = [];
        $anyListUpdated = false;

        foreach ($this->mListRepo->findAll() as $list) {
            $thisListUpdated = false;

            try {
                $res = $this->mc->getMember($list->getMcListId(), $mcId);

                if (isset($res['id']) && ($res['status'] === 'subscribed')) {
                    $list->addFeUser($user);
                    $subscribed[] = $list;
                    $thisListUpdated = true;
                } else {
                    if (!$newUser) {
                        $list->removeFeUser($user);
                        $thisListUpdated = true;
                    }
                }
            } catch (Exception $e) {
                // Assuming the response is a 404
                if (!$newUser) {
                    $list->removeFeUser($user);
                    $thisListUpdated = true;
                }
            }

            // If we've made any changes to list, mark it for update

            if ($thisListUpdated) {
                $this->mListRepo->update($list);
                $anyListUpdated = true;
            }
        }

        // If we've made any changes to any list, persist the changes to the database

        if ($anyListUpdated) {
            $this->pm->persistAll();
        }

        return $subscribed;
    }

    /**
     * Subscribe the given user to the given list.
     *
     * If $confirm is false, then the local database will be updated
     * immediately. If it's true, then the local database will not be updated.
     *
     * @param  \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user    User to subscribe to list
     * @param  \Tev\TevMailchimp\Domain\Model\Mlist         $list    List to subscribe user to
     * @param  boolean                                      $confirm Whether or not to require confirmation from the user
     * @param  array                                        $dataOverrides
     * @throws Exception
     */
    public function subscribeUserToList(FrontendUser $user, Mlist $list, $confirm = false, $dataOverrides = [])
    {
        $user = $this->castUser($user);

        $this->subscribeToList(
            $this->getEmailFromUser($user),
            $list,
            $confirm,
            $dataOverrides
        );

        if (!$confirm) {
            $list->addFeUser($user);
            $this->mListRepo->update($list);
            $this->pm->persistAll();
        }
    }

    /**
     * Subscribe the given email address to the given list.
     *
     * @param  string                               $email   Email to subscribe to list
     * @param  \Tev\TevMailchimp\Domain\Model\Mlist $list    List to subscribe email to
     * @param  boolean                              $confirm Whether or not to require confirmation from the user
     * @param  array                                $dataOverrides
     *
     * @throws Exception
     */
    public function subscribeToList($email, Mlist $list, $confirm = false, $dataOverrides = [])
    {
        try {
            $newStatus = $confirm ? 'pending' : 'subscribed';
            $curStatus = $this->getSubscriptionStatus($email, $list);

            if ($curStatus === null) {
                $base = [
                    'email_address' => $email,
                    'status' => $newStatus
                ];
                $detailsArr = array_merge($base, $dataOverrides);
                $this->mc->addMember($list->getMcListId(), $detailsArr);
            } elseif ($curStatus === 'unsubscribed'
                || $curStatus === 'pending'
                || $curStatus === 'cleaned'
            ) {
                $base = [
                    'status' => $newStatus
                ];
                $detailsArr = array_merge($base, $dataOverrides);
                $this->mc->updateMember($list->getMcListId(), $this->getMailchimpId($email), $detailsArr);
            }
        } catch (Exception $e) {
            $this->logger->error('MC API exception during subscribeToList', [
                'message' => $e->getMessage(),
                'email' => $email,
                'list_uid' => $list->getUid(),
                'mc_list_id' => $list->getMcListId()
            ]);

            throw $e;
        }
    }

    /**
     * Unsubscribe the given user from the given list.
     *
     * @param  \TYPO3\CMS\Extbase\Domain\Model\FrontendUser        $user User to unsubscribe from list
     * @param  \Tev\TevMailchimp\Domain\Model\Mlist                $list List to unsubscribe user from
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface       All lists the user is subscribed to
     */
    public function unsubscribeUserFromList(FrontendUser $user, Mlist $list)
    {
        $user = $this->castUser($user);

        $this->unsubscribeFromList($this->getEmailFromUser($user), $list);

        $list->removeFeUser($user);
        $this->mListRepo->update($list);
        $this->pm->persistAll();
    }

    /**
     * Unsubcribe the given email address from the given list.
     *
     * @param  string                               $email Email to unsubscribe from list
     * @param  \Tev\TevMailchimp\Domain\Model\Mlist $list  List to unsubscribe email from
     * @return void
     */
    public function unsubscribeFromList($email, Mlist $list)
    {
        try {
            $curStatus = $this->getSubscriptionStatus($email, $list);

            if ($curStatus === 'subscribed'
                || $curStatus === 'pending'
                || $curStatus === 'cleaned'
            ) {
                $this->mc->updateMember($list->getMcListId(), $this->getMailchimpId($email), [
                    'status' => 'unsubscribed'
                ]);
            }
        } catch (Exception $e) {
            $this->logger->error('MC API exception during unsubscribeFromList', [
                'message' => $e->getMessage(),
                'email' => $email,
                'list_uid' => $list->getUid(),
                'mc_list_id' => $list->getMcListId()
            ]);

            throw $e;
        }
    }

    /**
     * Retrieve the email address from the given user.
     *
     * @param  \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user
     * @return string
     */
    private function getEmailFromUser(FrontendUser $user)
    {
        return $user->{'get' . $this->emailUtil->getFieldNameUpperCamel()}();
    }

    /**
     * Get the Mailchimp ID for the given email address.
     *
     * @param  string $email Email address
     * @return string        Mailchimp ID
     */
    private function getMailchimpId($email)
    {
        return md5(strtolower($email));
    }

    /**
     * Cast a user that might be a child class to a base FE user.
     *
     * @param  \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    private function castUser(FrontendUser $user)
    {
        if (get_class($user) !== 'TYPO3\CMS\Extbase\Domain\Model\FrontendUser') {
            $user = $this->userRepo->findByUid($user->getUid());
        }

        return $user;
    }

    /**
     * Get the subscription status for the given email address and list.
     *
     * @param  string                               $email Email address
     * @param  \Tev\TevMailchimp\Domain\Model\Mlist $list  List to check
     * @return string|null                                 'subscribed', 'unsubscribed', 'pending', 'cleaned' or null if not subscribed at all
     */
    private function getSubscriptionStatus($email, Mlist $list)
    {
        try {
            $res = $this->mc->getMember($list->getMcListId(), $this->getMailchimpId($email));

            if (isset($res['id'])) {
                return $res['status'];
            } else {
                return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }
}
