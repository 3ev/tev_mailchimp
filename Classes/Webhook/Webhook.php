<?php
namespace Tev\TevMailchimp\Webhook;

use Carbon\Carbon;

/**
 * Mailchimp webhook object.
 *
 * See https://apidocs.mailchimp.com/webhooks/ for more information.
 */
class Webhook
{
    /**
     * Hook type.
     *
     * @var string
     */
    protected $type;

    /**
     * Hook fired at time (GMT).
     *
     * @var \Carbon\Carbon
     */
    protected $firedAt;

    /**
     * Hook data (without merge fields).
     *
     * @var array
     */
    protected $data;

    /**
     * Hook merge fields.
     *
     * @var array
     */
    protected $mergeFields;

    /**
     * Constructor.
     *
     * @param  string         $type        Hook type
     * @param  \Carbon\Carbon $firedAt     Hook fired at time (GMT)
     * @param  array          $data        Hook data (without merge fields)
     * @param  array          $mergeFields Hook merge fields
     * @return void
     */
    public function __construct($type, Carbon $firedAt, array $data, array $mergeFields = [])
    {
        $this->type = $type;
        $this->firedAt = $firedAt;
        $this->data = $data;
        $this->mergeFields = $mergeFields;
    }

    /**
     * Get the hook type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the hook fired at time.
     *
     * @return \Carbon\Carbon
     */
    public function getFiredAt()
    {
        return $this->firedAt;
    }

    /**
     * Get an piece of hook data.
     *
     * @param  string      $key Underscored key
     * @return string|null
     */
    public function getData($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        } else {
            return null;
        }
    }

    /**
     * Get merge field data.
     *
     * @param  string      $field Merge field name (uppercase)
     * @return string|null
     */
    public function getMergeField($field)
    {
        if (isset($this->mergeFields[$field])) {
            return $this->mergeFields[$field];
        } else {
            return null;
        }
    }
}
