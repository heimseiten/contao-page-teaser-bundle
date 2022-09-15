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

function getImage($page_id, $page_pid, $pages, $size_id, $without_text, $teaser_only_sub_pages) {        
        if ($pages) {
            if ($teaser_only_sub_pages) {
                $arrResults = \Database::getInstance()->query("SELECT `id`, `pid`, `title`, `pageTitle`, `description`, `teaser_headline`, `teaser_text`, `pageImage`, `target` FROM `tl_page` WHERE pid in (". implode(',',$pages) .") AND `type`!='folder' AND `type`!='error_404' AND `hide`!='1' AND `published`='1' AND `id`!='".$page_id."' ORDER BY `sorting`;")->fetchAllAssoc();    
            } else {
                $arrResults = \Database::getInstance()->query("SELECT `id`, `title`, `pageTitle`, `description`, `teaser_headline`, `teaser_text`, `pageImage`, `target` FROM `tl_page` WHERE id in (". implode(',',$pages) .") AND `type`!='folder' AND `type`!='error_404' AND `published`='1' AND `id`!='".$page_id."' ORDER BY `sorting`;")->fetchAllAssoc();    
            }
        } else {
            $arrResults = \Database::getInstance()->query(    "SELECT `id`, `title`, `pageTitle`, `description`, `teaser_headline`, `teaser_text`, `pageImage`, `target` FROM `tl_page` WHERE pid =". $page_id ." AND `type`!='folder' AND `type`!='error_404' AND `hide`!='1' AND `published`='1' ORDER BY `sorting`;")->fetchAllAssoc();    
            if ($arrResults == NULL){ 
                $arrResults = \Database::getInstance()->query("SELECT `id`, `pid`, `title`, `pageTitle`, `description`, `teaser_headline`, `teaser_text`, `pageImage`, `target` FROM `tl_page` WHERE pid =". $page_pid ." AND `type`!='folder' AND `type`!='error_404' AND `hide`!='1' AND `published`='1' AND `id`!='".$page_id."' ORDER BY `sorting`;")->fetchAllAssoc();    
            }            
        }
        $length = count($arrResults);
        for ($i = 0; $i < $length; $i++) {
            if ( $arrResults[$i]['teaser_headline'] ) {
                $link_name = $arrResults[$i]['teaser_headline']; } else {
                    if ( $arrResults[$i]['pageTitle'] ) { $link_name = $arrResults[$i]['pageTitle']; } else {
                        if ( $arrResults[$i]['title'] ) { $link_name = $arrResults[$i]['title']; }
                }
            }
            $link_name = strip_tags($link_name);
            $target = '';
            if ($arrResults[$i]['target']) { $target = ' target="_blank"'; }
            echo '<div class="ce_teaser_wrapper" '.$style.'>';
                echo '<div class="inside">
                        <a'.$target.' href="{{link_url::'.$arrResults[$i]['id'].'}}" class="ce_teaser_item" aria-label="' . $link_name . '">'; 
                    if ($arrResults[$i]['pageImage']) { 
                        renderPageImage( $arrResults[$i]['pageImage'], $size_id ); 
                    } else {
                        $pageId = $arrResults[$i]['id'];
                        $tl_article = \Database::getInstance()->query("SELECT id, pid, articleImage FROM `tl_article` WHERE pid=".$pageId." ORDER BY `sorting` LIMIT 4;")->fetchAllAssoc();
                        if ($tl_article[0]['articleImage'] !='') {
                            echo('{{picture::'.\FilesModel::findByUuid($tl_article[0]['articleImage'])->path.'?size='.$size_id.'}}');
                        } else {  
                            if ($tl_article[0]['id']) {
                                $tl_content = \Database::getInstance()->query("SELECT id, pid, invisible, singleSRC FROM `tl_content` WHERE pid=".$tl_article[0]['id']." AND `invisible`!='1' AND `singleSRC`!='' AND ((`addImage`='1') OR (`type`='image')) ORDER BY `sorting` LIMIT 5;")->fetchAllAssoc();
                                if ($tl_content[0]['singleSRC']!='') { 
                                    echo('{{picture::'.\FilesModel::findByUuid($tl_content[0]['singleSRC'])->path.'?size='.$size_id.'}}');
                                } else {
                                    if ($tl_article[1]['id']) {
                                        $tl_content = \Database::getInstance()->query("SELECT id, pid, invisible, singleSRC FROM `tl_content` WHERE pid=".$tl_article[1]['id']." AND `invisible`!='1' AND `singleSRC`!='' AND ((`addImage`='1') OR (`type`='image')) ORDER BY `sorting` LIMIT 5;")->fetchAllAssoc();
                                        if ($tl_content[1]['singleSRC']!='') { 
                                            echo('{{picture::'.\FilesModel::findByUuid($tl_content[0]['singleSRC'])->path.'?size='.$size_id.'}}');
                                        } else {
                                            if ($tl_article[2]['id']) {
                                                $tl_content = \Database::getInstance()->query("SELECT id, pid, invisible, singleSRC FROM `tl_content` WHERE pid=".$tl_article[2]['id']." AND `invisible`!='1' AND `singleSRC`!='' AND ((`addImage`='1') OR (`type`='image')) ORDER BY `sorting` LIMIT 5;")->fetchAllAssoc();
                                                if ($tl_content[2]['singleSRC']!='') { 
                                                    echo('{{picture::'.\FilesModel::findByUuid($tl_content[0]['singleSRC'])->path.'?size='.$size_id.'}}');
                                                } else {
                                                    if ($tl_article[3]['id']) {
                                                        $tl_content = \Database::getInstance()->query("SELECT id, pid, invisible, singleSRC FROM `tl_content` WHERE pid=".$tl_article[3]['id']." AND `invisible`!='1' AND `singleSRC`!='' AND ((`addImage`='1') OR (`type`='image')) ORDER BY `sorting` LIMIT 5;")->fetchAllAssoc();
                                                        echo('{{picture::'.\FilesModel::findByUuid($tl_content[0]['singleSRC'])->path.'?size='.$size_id.'}}');
                                                    }    
                                                }    
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    echo '<div class="text">';
                        echo '<h2>'.$link_name.'</h2>';
                        if (!$without_text) {
                            if ( $arrResults[$i]['teaser_text'] ) { echo '<p>'.$arrResults[$i]['teaser_text'].'</p>'; } 
                            else { if ( $arrResults[$i]['description'] ) { echo '<p>'.$arrResults[$i]['description'].'</p>'; } }
                        }
                    echo '</div>';
                echo '</a></div>';
            echo '</div>';
        }
}

function renderPageImage($pageImage, $size_id) {
    $multisrc = deserialize ($pageImage);
    $objFiles = \FilesModel::findMultipleByUuids ($multisrc);
    if ($objFiles) {
        while ( $objFiles->next () ) {
            $image_path = $objFiles->path;
            echo('{{picture::' . $image_path . '?size='.$size_id.'}}');
        }
    }
}
