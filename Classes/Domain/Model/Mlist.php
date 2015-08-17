<?php
namespace Tev\TevMailchimp\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;

/**
 * Entity class for Mailchimp lists.
 */
class Mlist extends AbstractEntity
{
    /**
     * The Mailchimp list name.
     *
     * @var string
     */
    protected $name;

    /**
     * The list description, entered in TYPO3.
     *
     * @var string
     */
    protected $description;

    /**
     * The Mailchimp list ID.
     *
     * @var string
     */
    protected $mcListId;

    /**
     * The date the list was created in Mailchimp.
     *
     * @var int
     */
    protected $mcCreatedAt;

    /**
     * List subscribers.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FrontendUser>
     * @lazy
     */
    protected $feUsers;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->feUsers = new ObjectStorage();
    }

    /**
     * Return the Mailchimp list name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the Mailchimp list name.
     *
     * @param  string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Return the list description, entered in TYPO3.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the list description, entered in TYPO3.
     *
     * @param  string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Return the Mailchimp list ID.
     *
     * @return string
     */
    public function getMcListId()
    {
        return $this->mcListId;
    }

    /**
     * Set the Mailchimp list ID.
     *
     * @param  string $mcListId
     * @return void
     */
    public function setMcListId($mcListId)
    {
        $this->mcListId = $mcListId;
    }

    /**
     * Return the date the list was created in Mailchimp.
     *
     * @return int
     */
    public function getMcCreatedAt()
    {
        return $this->mcCreatedAt;
    }

    /**
     * Set the date the list was created in Mailchimp.
     *
     * @param  string $mcCreatedAt
     * @return void
     */
    public function setMcCreatedAt($mcCreatedAt)
    {
        $this->mcCreatedAt = $mcCreatedAt;
    }

    /**
     * Add subscriber.
     *
     * @param  \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $feUser
     * @return void
     */
    public function addFeUser(FrontendUser $feUser)
    {
        $this->feUsers->attach($feUser);
    }

    /**
     * Remove subscriber.
     *
     * @param  \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $feUser
     * @return void
     */
    public function removeFeUser(FrontendUser $feUser)
    {
        $this->feUsers->detach($feUser);
    }

    /**
     * Returns subscribers.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FrontendUser>
     */
    public function getFeUsers()
    {
        return $this->feUsers;
    }

    /**
     * Set subscribers.
     *
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FrontendUser> $feUsers
     * @return void
     */
    public function setFeUsers(ObjectStorage $feUsers)
    {
        $this->feUsers = $feUsers;
    }
}
