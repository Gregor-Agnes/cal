<?php
/**
 * This file is part of the TYPO3 extension Calendar Base (cal).
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 * The TYPO3 extension Calendar Base (cal) project - inspiring people to share!
 */

namespace TYPO3\CMS\Cal\Cron;

use TYPO3\CMS\Cal\Controller\DateParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * IndexerScheduler
 */
class IndexAllScheduler extends AbstractTask
{

    public $starttime = '';

    public $endtime = '';

    public function execute()
    {
        $success = true;
        $logger = GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);

        $starttime = $this->getTimeParsed($this->starttime)->format('%Y%m%d');
        $endtime = $this->getTimeParsed($this->endtime)->format('%Y%m%d');

        $logger->info('Starting to index cal events from ' . $starttime . ' until ' . $endtime . '.');
        /** @var \TYPO3\CMS\Cal\Utility\RecurrenceGenerator $rgc */
        $rgc = GeneralUtility::makeInstance('TYPO3\\CMS\\Cal\\Utility\\RecurrenceGenerator', null, $starttime, $endtime);
        $logger->info('Working on all events.');
        $rgc->cleanIndexTableAll();
        $logger->info('Starting to index... ');
        $rgc->generateIndex(0);
        $logger->info('done.');
        $logger->info('IndexerScheduler done.');

        return $success;
    }

    private function getTimeParsed($timeString)
    {
        $dp = GeneralUtility::makeInstance('TYPO3\\CMS\\Cal\\Controller\\DateParser');
        $dp->parse($timeString, 0, '');

        return $dp->getDateObjectFromStack();
    }
}