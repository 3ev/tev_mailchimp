<?php
namespace Tev\TevMailchimp\Domain\Repository;

use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use Tev\TevMailchimp\Domain\Model\Mlist;

/**
 * Repository for Mailchimp list entities.
 */
class MlistRepository extends Repository
{
    /**
     * Configuration manager.
     *
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $cm;

    /**
     * {@inheritdoc}
     */
    public function initializeObject()
    {
        $this->defaultOrderings = [
            'name' => QueryInterface::ORDER_ASCENDING
        ];

        // Ensure correct storage PID is used, regardless of what plugin the
        // repository is injected into

        $config = $this->cm->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'tevmailchimp'
        );

        $querySettings = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings');
        $querySettings->setStoragePageIds([$config['persistence']['storagePid']]);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Create or update an existing list with the given Mailchimp List ID from
     * Mailchimp.
     *
     * @param  string $mcListId List ID
     * @param  array  $data     Fields
     * @return void
     */
    public function addOrUpdateFromMailchimp($mcListId, array $data)
    {
        if (!($list = $this->findHiddenOneByMcListId($mcListId))) {
            $list = new Mlist();
            $list->setMcListId($mcListId);
            $list->setMcCreatedAt($data['mc_created_at']);
            $list->setName($data['name']);
            $this->add($list);
        } else {
            $list->setName($data['name']);
            $this->update($list);
        }
    }

    /**
     * Soft delete all lists not in the given array of Mailchimp list IDs.
     *
     * @param  array $listIds Mailchimp list IDs to keep
     * @return void
     */
    public function deleteAllNotInList(array $listIds)
    {
        $q = $this->createQuery();

        $q->matching($q->logicalNot($q->in('mc_list_id', $listIds)));

        foreach ($q->execute() as $toRemove) {
            $this->remove($toRemove);
        }
    }

    /**
     * Find all of the lists subscribed to by the given user.
     *
     * @param  \TYPO3\CMS\Extbase\Domain\Model\FrontendUser        $user
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllSubscribedToBy(FrontendUser $user)
    {
        $q = $this->createQuery();

        $q->matching($q->contains('feUsers', [$user->getUid()]));

        $q->setOrderings(['sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING]);

        return $q->execute();
    }

    /**
     * Find all of the lists not subscribed to by the given user.
     *
     * @param  \TYPO3\CMS\Extbase\Domain\Model\FrontendUser        $user
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllNotSubscribedToBy(FrontendUser $user)
    {
        $q = $this->createQuery();

        $q->matching($q->logicalNot($q->contains('feUsers', [$user->getUid()])));

        $q->setOrderings(['sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING]);

        return $q->execute();
    }

    /**
     * Find list item by my_list_id even if it is hidden.
     *
     * @param  string                                               $mcListId
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findHiddenOneByMcListId($mcListId)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->matching($query->equals('mc_list_id', $mcListId));

        return $query->execute()->getFirst();
    }
}
