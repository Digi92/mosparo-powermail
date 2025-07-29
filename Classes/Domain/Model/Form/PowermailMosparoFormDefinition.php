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

namespace Mahou\MosparoPowermail\Domain\Model\Form;

use Denkwerk\MosparoForm\Domain\Model\Form\MosparoFormDefinitionInterface;
use In2code\Powermail\Domain\Model\Form as PowermailForm;

/**
 * Class PowermailMosparoFormDefinition
 * @package Mahou\MosparoPowermail\Domain\Model\Form
 */
class PowermailMosparoFormDefinition implements MosparoFormDefinitionInterface
{
    public function __construct(protected PowermailForm $powermailForm)
    {
    }

    public function getPowermailForm(): PowermailForm
    {
        return $this->powermailForm;
    }

    public function setPowermailForm(PowermailForm $powermailForm): void
    {
        $this->powermailForm = $powermailForm;
    }
}
