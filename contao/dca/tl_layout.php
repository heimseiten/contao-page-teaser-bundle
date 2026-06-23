<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;

// Add the teaser settings under the existing image sizes legend ("Bildgrößen"),
// for both the classic ("default") and the modern Twig ("modern") layout palette.
PaletteManipulator::create()
    ->addField(['teaser_image_size_id', 'teaser_without_text'], 'image_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_layout')
    ->applyToPalette('modern', 'tl_layout')
;

$GLOBALS['TL_DCA']['tl_layout']['fields'] += [
    'teaser_image_size_id' => [
        'exclude'          => true,
        'inputType'        => 'imageSize',
        'reference'        => &$GLOBALS['TL_LANG']['MSC'],
        'eval'             => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
        'options_callback' => static function () {
            return Contao\System::getContainer()->get('contao.image.sizes')->getOptionsForUser(Contao\BackendUser::getInstance());
        },
        'sql'              => "varchar(255) NOT NULL default ''",
    ],
    'teaser_without_text' => [
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''",
    ],
];
