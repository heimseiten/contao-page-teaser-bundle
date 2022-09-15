<?php
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;


$GLOBALS['TL_DCA']['tl_article']['fields']['articleImage'] = [ 
    'label'     => &$GLOBALS['TL_LANG']['tl_article']['articleImage'],
    'inputType' => 'fileTree',
    'eval' => array('tl_class'  => 'w50', 'fieldType' => 'radio', 'filesOnly' => true ),
    'sql'       => "blob NULL"
];
