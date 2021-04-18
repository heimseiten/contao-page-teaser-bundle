<?php

declare(strict_types=1);

namespace Heimseiten\ContaoArticleImageBundle\Listener;

use Contao\FrontendTemplate;
use Contao\Template;
use Contao\Module;

class HooksListener
{
    
    public function onCompileArticle(FrontendTemplate $objTemplate, array $arrData, Module $module): void
    {
        if (TL_MODE == 'FE') {
            $template = new FrontendTemplate('article_image');

            $template->articleImage = $arrData['articleImage'];
            $template->sizeId = deserialize($arrData['size'])[2];
            $template->articleVideo = $arrData['articleVideo'];
            $template->noBgVideoLoop = $arrData['noBgVideoLoop'];
            $template->viewBgVideoOnMobile = $arrData['viewBgVideoOnMobile'];
            $template->viewBgImageOnMobile = $arrData['viewBgImageOnMobile'];
            $template->verticalBgShift = $arrData['verticalBgShift'];
            $template->bgParallax = $arrData['bgParallax'];
            $template->BgCssFilter = $arrData['BgCssFilter'];
            
            $elements = $objTemplate->elements;
            array_unshift($elements, $template->parse());
            $objTemplate->elements = $elements;
        }
    }

    public function onParseTemplate(Template $objTemplate)
    {
        if (TL_MODE == 'FE' && $objTemplate->type == 'article') {

            $objTemplate->class .= ' ' . implode(' ', $arrClasses);
            $objTemplate->style .= 'margin-top:20px;';
        }
    }

}
