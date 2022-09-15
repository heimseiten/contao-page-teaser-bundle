<?php

use Contao\Config;
use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addLegend('teaser_legend',        'image_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('teaser_image_size_id',  'teaser_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('teaser_without_text',   'teaser_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_layout') 
;

$GLOBALS['TL_DCA']['tl_layout']['fields'] += [    
    'teaser_image_size_id' => [
            'exclude'                 => true,
			'inputType'               => 'imageSize',
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
			'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
			'options_callback' => static function ()
			{
				return Contao\System::getContainer()->get('contao.image.sizes')->getOptionsForUser(Contao\BackendUser::getInstance());
			},
			'sql'                     => "varchar(255) NOT NULL default ''"
    ],
    'teaser_without_text' => [
        'inputType'     => 'checkbox', 
        'sql'           => "char(1) NOT NULL default ''"
    ],
];
