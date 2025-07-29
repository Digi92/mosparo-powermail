<?php
declare(strict_types=1);

/*
 * This file is part of the "mosparo-powermail" Extension for TYPO3 CMS.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Mahou\MosparoPowermail\Domain\Model\Powermail;

use In2code\Powermail\Domain\Model\Field as PowerMailField;

/**
 * Class Field
 * @package Mahou\MosparoPowermail\Domain\Model\Powermail
 */
class Field extends PowerMailField
{
    protected string $txMosparopowermailSelectedProject;

    public function getTxMosparopowermailSelectedProject(): string
    {
        return $this->txMosparopowermailSelectedProject;
    }

    public function setTxMosparopowermailSelectedProject(string $txMosparopowermailSelectedProject): void
    {
        $this->txMosparopowermailSelectedProject = $txMosparopowermailSelectedProject;
    }
}
