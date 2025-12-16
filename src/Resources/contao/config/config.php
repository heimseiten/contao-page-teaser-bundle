<?php
use Contao\StringUtil;

/**
 * Sucht das erste verfügbare Bild in den Artikeln einer Seite
 */
function findArticleImage($db, $pageId, $size_id) {
    $articles = $db->executeQuery(
        "SELECT id FROM `tl_article` WHERE pid=" . $pageId . " ORDER BY `sorting` LIMIT 4"
    )->fetchAll();

    foreach ($articles as $article) {
        if (empty($article['id'])) {
            continue;
        }

        $content = $db->executeQuery(
            "SELECT singleSRC FROM `tl_content`
             WHERE pid=" . $article['id'] . "
             AND `invisible`!='1'
             AND `singleSRC`!=''
             AND ((`addImage`='1') OR (`type`='image'))
             ORDER BY `sorting` LIMIT 1"
        )->fetch();

        if (!empty($content['singleSRC'])) {
            renderPageImage($content['singleSRC'], $size_id);
            return true;
        }
    }

    return false;
}

function getImage($page_id, $page_pid, $pages, $size_id, $without_text, $teaser_only_sub_pages) {
    $db = \Contao\System::getContainer()->get('database_connection');

    // Prüfe ob pageImage Spalte existiert
    $hasPageImageColumn = $db->executeQuery("SHOW COLUMNS FROM `tl_page` LIKE 'pageImage'")->rowCount() > 0;
    $pageImageField = $hasPageImageColumn ? "`pageImage`," : "";

    // Hole Seiten basierend auf Parametern
    if ($pages) {
        if ($teaser_only_sub_pages) {
            $arrResults = $db->executeQuery(
                "SELECT `id`, `pid`, `title`, `pageTitle`, `description`, `teaser_headline`, `teaser_text`, " . $pageImageField . " `target`, `cssClass`
                 FROM `tl_page`
                 WHERE pid IN (" . implode(',', $pages) . ")
                 AND `type`!='folder'
                 AND `type`!='error_404'
                 AND `hide`!='1'
                 AND `published`='1'
                 AND `id`!='" . $page_id . "'
                 ORDER BY FIELD(pid, " . implode(',', $pages) . ")"
            )->fetchAll();
        } else {
            $arrResults = $db->executeQuery(
                "SELECT `id`, `title`, `pageTitle`, `description`, `teaser_headline`, `teaser_text`, " . $pageImageField . " `target`, `cssClass`
                 FROM `tl_page`
                 WHERE id IN (" . implode(',', $pages) . ")
                 AND `type`!='folder'
                 AND `type`!='error_404'
                 AND `published`='1'
                 AND `id`!='" . $page_id . "'
                 ORDER BY FIELD(id, " . implode(',', $pages) . ")"
            )->fetchAll();
        }
    } else {
        $arrResults = $db->executeQuery(
            "SELECT `id`, `title`, `pageTitle`, `description`, `teaser_headline`, `teaser_text`, " . $pageImageField . " `target`, `cssClass`
             FROM `tl_page`
             WHERE pid=" . $page_id . "
             AND `type`!='folder'
             AND `type`!='error_404'
             AND `hide`!='1'
             AND `published`='1'
             ORDER BY `sorting`"
        )->fetchAll();

        if (empty($arrResults)) {
            $arrResults = $db->executeQuery(
                "SELECT `id`, `pid`, `title`, `pageTitle`, `description`, `teaser_headline`, `teaser_text`, " . $pageImageField . " `target`, `cssClass`
                 FROM `tl_page`
                 WHERE pid=" . $page_pid . "
                 AND `type`!='folder'
                 AND `type`!='error_404'
                 AND `hide`!='1'
                 AND `published`='1'
                 AND `id`!='" . $page_id . "'
                 ORDER BY `sorting`"
            )->fetchAll();
        }
    }

    // Rendere jeden Teaser
    foreach ($arrResults as $page) {
        // Bestimme Link-Namen
        $link_name = !empty($page['teaser_headline']) ? $page['teaser_headline'] : $page['title'];
        $link_name = strip_tags($link_name);

        // Bestimme Target-Attribut
        $target = !empty($page['target']) ? ' target="_blank"' : '';

        // Wrapper öffnen
        echo '<div class="ce_teaser_wrapper ' . $page['cssClass'] . '">';
        echo '<div class="inside">';
        echo '<a' . $target . ' href="{{link_url::' . $page['id'] . '}}" class="ce_teaser_item" aria-label="' . $link_name . '">';

        // Bild ausgeben
        if (!empty($page['pageImage'])) {
            renderPageImage($page['pageImage'], $size_id);
        } else {
            findArticleImage($db, $page['id'], $size_id);
        }

        // Text ausgeben
        echo '<div class="text">';
        echo '<h2>' . $link_name . '</h2>';

        if (!$without_text) {
            $description = !empty($page['teaser_text']) ? $page['teaser_text'] : $page['description'];
            if ($description) {
                echo '<p>' . $description . '</p>';
            }
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
