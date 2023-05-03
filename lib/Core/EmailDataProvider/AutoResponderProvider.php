<?php


namespace Netgen\InformationCollection\Core\EmailDataProvider;

use Netgen\InformationCollection\Core\Factory\AutoResponderDataFactory;

class AutoResponderProvider extends DefaultProvider
{
    // could be replaced by pure service configuration
    public function __construct(
        AutoResponderDataFactory $emailDataFactory
    ) {
        parent::__construct($emailDataFactory);
    }
}
