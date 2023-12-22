<?php

/**
 * (c) Kitodo. Key to digital objects e.V. <contact@kitodo.org>
 *
 * This file is part of the Kitodo and TYPO3 projects.
 *
 * @license GNU General Public License version 3 or later.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
namespace Kitodo\Dlf\Task;

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Core\Environment;

/**
 * Task for index a document.
 *
 * @package TYPO3
 * @subpackage dlf
 *
 * @access public
 */
class IndexTask extends BaseTask
{
    public function execute()
    {
        $inputArray = [];
        if ($this->dryRun) {
            $inputArray['--dry-run'] = true;
        }
        $inputArray['-d'] = $this->doc;
        $inputArray['-p'] = $this->pid;
        $inputArray['-s'] = $this->solr;
        if (! empty($this->owner)) {
            $inputArray['-o'] = $this->owner;
        }

        $indexCommand = GeneralUtility::makeInstance(\Kitodo\Dlf\Command\IndexCommand::class);
        $inputInterface = GeneralUtility::makeInstance(\Symfony\Component\Console\Input\ArrayInput::class, $inputArray);
        if (Environment::isCli()) {
            $outputInterface = GeneralUtility::makeInstance(\Symfony\Component\Console\Output\ConsoleOutput::class);
        } else {
            $outputInterface = GeneralUtility::makeInstance(\Symfony\Component\Console\Output\BufferedOutput::class);
        }

        $return = $indexCommand->run($inputInterface, $outputInterface);

        if (!Environment::isCli()) {
            $this->outputFlashMessages($outputInterface->fetch(), $return ? FlashMessage::ERROR : FlashMessage::OK);
        }
        return !$return;
    }
}