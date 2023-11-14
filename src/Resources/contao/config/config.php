<?php
use Contao\StringUtil;  

function getImage($page_id, $page_pid, $pages, $size_id, $without_text, $teaser_only_sub_pages) {        
        $db = \Contao\System::getContainer()->get('database_connection');
        $arrResults = $db->executeQuery("SELECT id, teaser_image_size_id, teaser_without_text FROM `tl_layout` WHERE id =". $GLOBALS['objPage']->layoutId ." ;")->fetch(); 

        if ($pages) {
            if ($teaser_only_sub_pages) {
                $arrResults = $db->executeQuery("SELECT `id`, `pid`, `title`, `pageTitle`, `description`, `teaser_headline`, `teaser_text`, `pageImage`, `target`, `cssClass` FROM `tl_page` WHERE pid in (". implode(',',$pages) .") AND `type`!='folder' AND `type`!='error_404' AND `hide`!='1' AND `published`='1' AND `id`!='".$page_id."' ORDER BY `sorting`;")->fetchAll();    
            } else {
                $arrResults = $db->executeQuery("SELECT `id`, `title`, `pageTitle`, `description`, `teaser_headline`, `teaser_text`, `pageImage`, `target`, `cssClass` FROM `tl_page` WHERE id in (". implode(',',$pages) .") AND `type`!='folder' AND `type`!='error_404' AND `published`='1' AND `id`!='".$page_id."' ORDER BY `sorting`;")->fetchAll();    
            }
        } else {
            $arrResults = $db->executeQuery("SELECT `id`, `title`, `pageTitle`, `description`, `teaser_headline`, `teaser_text`, `pageImage`, `target`, `cssClass` FROM `tl_page` WHERE pid =". $page_id ." AND `type`!='folder' AND `type`!='error_404' AND `hide`!='1' AND `published`='1' ORDER BY `sorting`;")->fetchAll();    
            if ($arrResults == NULL){ 
                $arrResults = $db->executeQuery("SELECT `id`, `pid`, `title`, `pageTitle`, `description`, `teaser_headline`, `teaser_text`, `pageImage`, `target`, `cssClass` FROM `tl_page` WHERE pid =". $page_pid ." AND `type`!='folder' AND `type`!='error_404' AND `hide`!='1' AND `published`='1' AND `id`!='".$page_id."' ORDER BY `sorting`;")->fetchAll();    
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
            echo '<div class="ce_teaser_wrapper ' . $arrResults[$i]['cssClass'] . '" '.$style.'>';
                echo '<div class="inside">
                        <a'.$target.' href="{{link_url::'.$arrResults[$i]['id'].'}}" class="ce_teaser_item" aria-label="' . $link_name . '">'; 
                    if ($arrResults[$i]['pageImage']) { 
                        renderPageImage( $arrResults[$i]['pageImage'], $size_id ); 
                    } else {
                        $pageId = $arrResults[$i]['id'];
                        $tl_article = $db->executeQuery("SELECT id, pid FROM `tl_article` WHERE pid=".$pageId." ORDER BY `sorting` LIMIT 4;")->fetchAll();
                        if ($tl_article[0]['id']) {
                            $tl_content = $db->executeQuery("SELECT id, pid, invisible, singleSRC FROM `tl_content` WHERE pid=".$tl_article[0]['id']." AND `invisible`!='1' AND `singleSRC`!='' AND ((`addImage`='1') OR (`type`='image')) ORDER BY `sorting` LIMIT 5;")->fetchAll();
                            if ($tl_content[0]['singleSRC']!='') { 
                                echo('{{picture::'.\Contao\FilesModel::findByUuid($tl_content[0]['singleSRC'])->path.'?size='.$size_id.'}}');
                            } else {
                                if ($tl_article[1]['id']) {
                                    $tl_content = $db->executeQuery("SELECT id, pid, invisible, singleSRC FROM `tl_content` WHERE pid=".$tl_article[1]['id']." AND `invisible`!='1' AND `singleSRC`!='' AND ((`addImage`='1') OR (`type`='image')) ORDER BY `sorting` LIMIT 5;")->fetchAll();
                                    if ($tl_content[1]['singleSRC']!='') { 
                                        echo('{{picture::'.\Contao\FilesModel::findByUuid($tl_content[0]['singleSRC'])->path.'?size='.$size_id.'}}');
                                    } else {
                                        if ($tl_article[2]['id']) {
                                            $tl_content = $db->executeQuery("SELECT id, pid, invisible, singleSRC FROM `tl_content` WHERE pid=".$tl_article[2]['id']." AND `invisible`!='1' AND `singleSRC`!='' AND ((`addImage`='1') OR (`type`='image')) ORDER BY `sorting` LIMIT 5;")->fetchAll();
                                            if ($tl_content[2]['singleSRC']!='') { 
                                                echo('{{picture::'.\Contao\FilesModel::findByUuid($tl_content[0]['singleSRC'])->path.'?size='.$size_id.'}}');
                                            } else {
                                                if ($tl_article[3]['id']) {
                                                    $tl_content = $db->executeQuery("SELECT id, pid, invisible, singleSRC FROM `tl_content` WHERE pid=".$tl_article[3]['id']." AND `invisible`!='1' AND `singleSRC`!='' AND ((`addImage`='1') OR (`type`='image')) ORDER BY `sorting` LIMIT 5;")->fetchAll();
                                                    echo('{{picture::'.\Contao\FilesModel::findByUuid($tl_content[0]['singleSRC'])->path.'?size='.$size_id.'}}');
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
    $multisrc = StringUtil::deserialize ($pageImage);
    $objFiles = \Contao\FilesModel::findMultipleByUuids ($multisrc);
    if ($objFiles) {
        while ( $objFiles->next () ) {
            $image_path = $objFiles->path;
            echo('{{picture::' . $image_path . '?size='.$size_id.'}}');
        }
    }
}
