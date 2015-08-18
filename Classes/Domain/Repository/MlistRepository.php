<?php
namespace Tev\TevMailchimp\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use Tev\TevMailchimp\Domain\Model\Mlist;

/**
 * Repository for Mailchimp list entities.
 */
class MlistRepository extends Repository
{
    /**
     * {@inheritdoc}
     */
    public function initializeObject()
    {
        $this->defaultOrderings = [
            'name' => QueryInterface::ORDER_ASCENDING
        ];
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
        if (!($list = $this->findOneByMcListId($mcListId))) {
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
}
