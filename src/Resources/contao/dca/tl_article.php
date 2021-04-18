<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;

PaletteManipulator::create()
    ->addLegend('article_bg', 'title_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('articleImage', 'article_bg', PaletteManipulator::POSITION_APPEND)
    ->addField('articleVideo', 'article_bg', PaletteManipulator::POSITION_APPEND)
    ->addField('size', 'article_bg', PaletteManipulator::POSITION_APPEND)
    ->addField('verticalBgShift', 'article_bg', PaletteManipulator::POSITION_APPEND)
    ->addField('viewBgImageOnMobile', 'article_bg', PaletteManipulator::POSITION_APPEND)
    ->addField('viewBgVideoOnMobile','article_bg', PaletteManipulator::POSITION_APPEND)
    ->addField('bgParallax', 'article_bg', PaletteManipulator::POSITION_APPEND)
    ->addField('noBgVideoLoop', 'article_bg', PaletteManipulator::POSITION_APPEND)
    ->addField('BgCssFilter', 'article_bg', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_article');

$GLOBALS['TL_DCA']['tl_article']['fields']['articleImage'] = [ 
    'label'     => &$GLOBALS['TL_LANG']['tl_article']['articleImage'],
    'inputType' => 'fileTree',
    'eval' => array('tl_class'  => 'w50', 'fieldType' => 'radio', 'filesOnly' => true, 'extensions' => \Config::get('validImageTypes') ),
    'sql'       => "blob NULL"
];
$GLOBALS['TL_DCA']['tl_article']['fields']['articleVideo'] = [ 
    'label'     => &$GLOBALS['TL_LANG']['tl_article']['articleVideo'],
    'inputType' => 'fileTree',
    'eval' => array('tl_class'  => 'w50', 'fieldType' => 'radio', 'filesOnly' => true, 'extensions' => 'mp4' ),
    'sql'       => "blob NULL"
];
$GLOBALS['TL_DCA']['tl_article']['fields']['size'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_article']['size'],
    'exclude' => true,
    'inputType' => 'imageSize',
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval' => [
        'rgxp' => 'natural',
        'includeBlankOption' => true,
        'nospace' => true,
        'helpwizard' => true,
        'tl_class' => 'clr w50',
    ],
    'options_callback' => function () {
        return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
    },
    'sql' => "varchar(64) NOT NULL default ''"
];
$GLOBALS['TL_DCA']['tl_article']['fields']['verticalBgShift'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_article']['verticalBgShift'],
    'inputType' => 'text',
    'eval'      => array('tl_class'=>'w50'),
    'sql'       => "text NULL"
];
$GLOBALS['TL_DCA']['tl_article']['fields']['viewBgVideoOnMobile'] = [ 
    'label'     => &$GLOBALS['TL_LANG']['tl_article']['viewBgVideoOnMobile'], 
    'inputType' => 'checkbox', 
    'eval'      => array('tl_class' => 'w50'),
    'sql'       => "char(1) NOT NULL default ''" 
];
$GLOBALS['TL_DCA']['tl_article']['fields']['viewBgImageOnMobile'] = [ 
    'label'     => &$GLOBALS['TL_LANG']['tl_article']['viewBgImageOnMobile'], 
    'inputType' => 'checkbox', 
    'eval'      => array('tl_class' => 'w50'),
    'sql'       => "char(1) NOT NULL default ''" 
];
$GLOBALS['TL_DCA']['tl_article']['fields']['noBgVideoLoop'] = [ 
    'label'     => &$GLOBALS['TL_LANG']['tl_article']['noBgVideoLoop'], 
    'inputType' => 'checkbox', 
    'eval'      => array('tl_class' => 'w50'),
    'sql'       => "char(1) NOT NULL default ''" 
];
$GLOBALS['TL_DCA']['tl_article']['fields']['bgParallax'] = [ 
    'label'     => &$GLOBALS['TL_LANG']['tl_article']['bgParallax'], 
    'inputType' => 'checkbox', 
    'eval'      => array('tl_class' => 'w50'),
    'sql'       => "char(1) NOT NULL default ''" 
];
$GLOBALS['TL_DCA']['tl_article']['fields']['BgCssFilter'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_article']['BgCssFilter'],
    'inputType' => 'text',
    'eval'      => array('tl_class'=>'clr'),
    'sql'       => "text NULL"
];
