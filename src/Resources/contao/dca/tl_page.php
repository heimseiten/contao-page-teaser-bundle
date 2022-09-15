<?php

use Contao\Config;
use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addLegend('teaser_legend', 'title_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('pageImage','teaser_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('teaser_headline','teaser_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('teaser_text',    'teaser_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('regular', 'tl_page')
    ->applyToPalette('forward', 'tl_page') 
    ->applyToPalette('redirect', 'tl_page')  
;

$GLOBALS['TL_DCA']['tl_page']['fields'] += [    
    'pageImage' => [
        'label'         => &$GLOBALS['TL_LANG']['tl_page']['pageImage'],
        'inputType'     => 'fileTree',
        'exclude'       => true,
        'eval'          => array('fieldType'=>'checkbox', 'orderField'=>'pageImageOrder', 'multiple'=>true, 'files'=>true, 'filesOnly'=>true, 'extensions'=> Config::get('validImageTypes'), 'isGallery'=>true),
        'sql'           => 'blob NULL'
    ],
    'pageImageOrder' => [
        'eval'          => array('doNotShow'=>true),
        'sql'           => 'blob NULL'
    ],
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
];
