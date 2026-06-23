<?php

/*
 * This file is part of Teaser.
 * 
 * (c) Niels Hegmans 2020 <info@heimseiten.de>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/heimseiten/contao-page-teaser-bundle
 */

/**
 * Content elements*/

$GLOBALS['TL_DCA']['tl_content']['palettes']['teaser_element'] = '{type_legend},type;{teaser_legend},teaser_pages,teaser_order,teaser_only_sub_pages,teaser_template;{teaser_appearance_legend},teaser_max_width,teaser_min_width;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields'] += [
    'teaser_pages' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['teaser_pages'],
        'exclude' => true,
        'inputType' => 'pageTree',
        'foreignKey' => 'tl_page.title',
        'eval' => [
            'multiple' => true,
            'fieldType' => 'checkbox',
            'files' => true,
            'isSortable' => true,
            'mandatory' => false,
        ],
        'sql' => 'blob NULL',
        'relation' => [
            'type' => 'hasMany',
            'load' => 'lazy',
        ],
    ],
    'teaser_only_sub_pages' => [
        'inputType'     => 'checkbox', 
        'sql'           => "char(1) NOT NULL default ''"
    ],
    'teaser_max_width' => [
        'inputType'    => 'text',
        'eval'         => ['tl_class'  => 'w50'],
        'sql'          => "char(10) NOT NULL default ''"
    ],
    'teaser_min_width' => [
        'inputType'    => 'text',
        'eval'         => ['tl_class'  => 'w50'],
        'sql'          => "char(10) NOT NULL default ''"
    ],
];
