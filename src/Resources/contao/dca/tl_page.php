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
 * page
 */

use Contao\Config;
use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addLegend('teaser_legend', 'image_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('teaser_headline','teaser_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('teaser_text',    'teaser_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('regular', 'tl_page') 
;
PaletteManipulator::create()
    ->addLegend('teaser_legend_root', 'meta_legend', PaletteManipulator::POSITION_BEFORE, true)
    ->addField('teaser_image_size_id','teaser_legend_root', PaletteManipulator::POSITION_APPEND)
    ->addField('teaser_without_text','teaser_legend_root', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('rootfallback', 'tl_page') 
;
PaletteManipulator::create()
    ->addLegend('teaser_legend_root', 'meta_legend', PaletteManipulator::POSITION_BEFORE, true)
    ->addField('teaser_image_size_id','teaser_legend_root', PaletteManipulator::POSITION_APPEND)
    ->addField('teaser_without_text','teaser_legend_root', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('root', 'tl_page') 
;

$GLOBALS['TL_DCA']['tl_page']['fields'] += [    
    'teaser_headline' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_page']['teaser_headline'], 
        'inputType' => 'text', 
        'sql'       => "text NULL" 
    ],
    'teaser_text' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_page']['teaser_text'],
        'inputType' => 'textarea',
        'sql'       => "text NULL"
    ],
    'teaser_image_size_id' => [
        'inputType'        => 'imageSize',
        'options'          => \System::getImageSizes(),
        'reference'        => &$GLOBALS['TL_LANG']['MSC'],
        'eval'             => array( 'rgxp' => 'digit', 'includeBlankOption' => true, 'tl_class'  => 'clr' ),
        'sql'              => "varchar(64) NOT NULL default ''"
    ],
    'teaser_without_text' => [
        'inputType'     => 'checkbox', 
        'sql'           => "char(1) NOT NULL default ''"
    ],
];
