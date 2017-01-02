<?php
namespace Block\Service;

use DateTime;

class PublishingOptions
{

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var DateTime
     */
    public $liveFrom;

    /**
     *
     * @var DateTime
     */
    public $expiresEnd;

    /**
     *
     * @var int
     */
    public $templateId;

}
