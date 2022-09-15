<?php

use Contao\Config;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\System;

PaletteManipulator::create()
    ->addLegend('teaser_legend',        'image_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('teaser_image_size_id',  'teaser_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('teaser_without_text',   'teaser_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_layout') 
;

$GLOBALS['TL_DCA']['tl_layout']['fields'] += [    
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
