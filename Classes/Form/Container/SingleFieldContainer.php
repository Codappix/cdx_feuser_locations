<?php
namespace Codappix\CdxFeuserLocations\Form\Container;

/*
 * Copyright (C) 2017  Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use TYPO3\CMS\Backend\Form\Container\SingleFieldContainer as CoreSingleFieldContainer;

/**
 * Extend original SingleFieldContainer to allow render type resolving for
 * passthrough fields.
 *
 * Workaround for https://forge.typo3.org/issues/82762
 */
class SingleFieldContainer extends CoreSingleFieldContainer
{
    public function render() : array
    {
        $resultArray = parent::render();
        $fieldConfig = $this->data['processedTca']['columns'][$this->data['fieldName']];

        if ($fieldConfig['config']['type'] !== 'passthrough') {
            return $resultArray;
        }

        $options = $this->data;
        $options['parameterArray'] = [
            'fieldConf' => $fieldConfig,
            'itemFormElValue' => $this->data['databaseRow'][$this->data['fieldName']],
        ];
        $options['renderType'] = 'passthrough';

        if (!empty($options['parameterArray']['fieldConf']['config']['renderType'])) {
            $options['renderType'] = $options['parameterArray']['fieldConf']['config']['renderType'];
        }

        return $this->nodeFactory->create($options)->render();
    }
}
