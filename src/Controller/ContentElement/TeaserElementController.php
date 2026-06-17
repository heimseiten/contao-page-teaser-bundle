<?php

declare(strict_types=1);

/*
 * This file is part of the "Seitenteaser" bundle.
 *
 * (c) Niels Hegmans <info@heimseiten.de>
 *
 * @license GPL-3.0-or-later
 * @link https://github.com/heimseiten/contao-page-teaser-bundle
 */

namespace Heimseiten\ContaoPageTeaserBundle\Controller\ContentElement;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Figure;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Date;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Renders a list of teasers for other pages.
 *
 * For each target page a title, a short text and a teaser image are collected.
 * The image is taken from the page image (terminal42/contao-pageimage) if set,
 * otherwise the first visible image found in the visible content elements of the
 * page's visible articles is used.
 */
#[AsContentElement(type: 'teaser_element', category: 'links', template: 'content_element/teaser_element')]
class TeaserElementController extends AbstractContentElementController
{
    public function __construct(
        private readonly Studio $studio,
        private readonly ContentUrlGenerator $urlGenerator,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        [$size, $withoutText] = $this->getLayoutConfig();

        $items = [];

        foreach ($this->findTeaserPages($model) as $page) {
            try {
                $href = $this->urlGenerator->generate($page, [], UrlGeneratorInterface::ABSOLUTE_PATH);
            } catch (RoutingException) {
                // Skip pages that cannot be turned into a URL (e.g. folders)
                continue;
            }

            $items[] = [
                'href' => $href,
                'title' => strip_tags((string) ($page->teaser_headline ?: $page->pageTitle ?: $page->title)),
                'text' => $withoutText ? '' : strip_tags((string) ($page->teaser_text ?: $page->description)),
                'newWindow' => (bool) $page->target,
                'cssClass' => (string) $page->cssClass,
                'figure' => $this->buildFigure($page, $size),
            ];
        }

        $template->set('items', $items);
        $template->set('teaser_max_width', $model->teaser_max_width);
        $template->set('teaser_min_width', $model->teaser_min_width);

        return $template->getResponse();
    }

    /**
     * Reads the teaser image size and the "hide text" flag from the current layout.
     *
     * @return array{0: array|int|string|null, 1: bool}
     */
    private function getLayoutConfig(): array
    {
        $page = $this->getPageModel();

        if (!$page instanceof PageModel) {
            return [null, false];
        }

        $page->loadDetails();

        $layout = LayoutModel::findByPk($page->layoutId);

        if (!$layout instanceof LayoutModel) {
            return [null, false];
        }

        $size = StringUtil::deserialize($layout->teaser_image_size_id);

        // The image size picker stores [width, height, sizeId]; only use it when set
        if (!\is_array($size) || empty($size[2])) {
            $size = null;
        }

        return [$size, (bool) $layout->teaser_without_text];
    }

    /**
     * Resolves the target pages of the teaser.
     *
     * @return array<PageModel>
     */
    private function findTeaserPages(ContentModel $model): array
    {
        $selected = array_values(array_filter(array_map(
            'intval',
            StringUtil::deserialize($model->teaser_pages, true),
        )));

        $current = $this->getPageModel();
        $currentId = $current instanceof PageModel ? (int) $current->id : null;

        // Explicit page selection
        if ($selected) {
            return $model->teaser_only_sub_pages
                ? $this->findVisibleChildren($selected, $currentId, true)
                : $this->findVisiblePages($selected, $currentId);
        }

        if (!$current instanceof PageModel) {
            return [];
        }

        // No selection: children of the current page, falling back to its siblings
        $pages = $this->findVisibleChildren([(int) $current->id], null, true);

        if (!$pages) {
            $pages = $this->findVisibleChildren([(int) $current->pid], $currentId, true);
        }

        return $pages;
    }

    /**
     * @param array<int> $pids
     *
     * @return array<PageModel>
     */
    private function findVisibleChildren(array $pids, int|null $excludeId, bool $excludeHidden): array
    {
        if (!$pids) {
            return [];
        }

        $time = Date::floorToMinute();
        $pidList = implode(',', $pids);

        $columns = [
            "tl_page.pid IN ($pidList)",
            "tl_page.type != 'folder'",
            "tl_page.type != 'error_404'",
            "tl_page.published = '1'",
            "(tl_page.start = '' OR tl_page.start <= $time)",
            "(tl_page.stop = '' OR tl_page.stop > $time)",
        ];

        if ($excludeHidden) {
            $columns[] = "tl_page.hide = ''";
        }

        if ($excludeId) {
            $columns[] = "tl_page.id != $excludeId";
        }

        $order = \count($pids) > 1 ? "FIELD(tl_page.pid, $pidList), tl_page.sorting" : 'tl_page.sorting';

        $pages = PageModel::findBy($columns, [], ['order' => $order]);

        return $pages ? $pages->getModels() : [];
    }

    /**
     * @param array<int> $ids
     *
     * @return array<PageModel>
     */
    private function findVisiblePages(array $ids, int|null $excludeId): array
    {
        if (!$ids) {
            return [];
        }

        $time = Date::floorToMinute();
        $idList = implode(',', $ids);

        $columns = [
            "tl_page.id IN ($idList)",
            "tl_page.type != 'folder'",
            "tl_page.type != 'error_404'",
            "tl_page.published = '1'",
            "(tl_page.start = '' OR tl_page.start <= $time)",
            "(tl_page.stop = '' OR tl_page.stop > $time)",
        ];

        if ($excludeId) {
            $columns[] = "tl_page.id != $excludeId";
        }

        $pages = PageModel::findBy($columns, [], ['order' => "FIELD(tl_page.id, $idList)"]);

        return $pages ? $pages->getModels() : [];
    }

    /**
     * Builds the teaser image: the page image has precedence, otherwise the first
     * visible image of the page's visible content elements is used.
     */
    private function buildFigure(PageModel $page, array|int|string|null $size): Figure|null
    {
        // 1. Page image (terminal42/contao-pageimage)
        foreach (StringUtil::deserialize($page->pageImage, true) as $uuid) {
            if ($figure = $this->createFigure($uuid, $size)) {
                return $figure;
            }
        }

        // 2. First visible image in the visible articles of the page
        $time = Date::floorToMinute();
        $pageId = (int) $page->id;

        $articles = ArticleModel::findBy(
            [
                "tl_article.pid = $pageId",
                "tl_article.published = '1'",
                "(tl_article.start = '' OR tl_article.start <= $time)",
                "(tl_article.stop = '' OR tl_article.stop > $time)",
            ],
            [],
            ['order' => 'tl_article.sorting'],
        );

        if (null === $articles) {
            return null;
        }

        foreach ($articles as $article) {
            $elements = ContentModel::findPublishedByPidAndTable((int) $article->id, 'tl_article');

            if (null === $elements) {
                continue;
            }

            foreach ($elements as $element) {
                if (empty($element->singleSRC) || ('image' !== $element->type && !$element->addImage)) {
                    continue;
                }

                if ($figure = $this->createFigure($element->singleSRC, $size)) {
                    return $figure;
                }
            }
        }

        return null;
    }

    private function createFigure(mixed $uuid, array|int|string|null $size): Figure|null
    {
        if (empty($uuid)) {
            return null;
        }

        if (Validator::isBinaryUuid($uuid)) {
            $uuid = StringUtil::binToUuid($uuid);
        }

        if (!\is_string($uuid) || !Validator::isStringUuid($uuid)) {
            return null;
        }

        try {
            return $this->studio
                ->createFigureBuilder()
                ->fromUuid($uuid)
                ->setSize($size)
                ->buildIfResourceExists()
            ;
        } catch (\Throwable) {
            return null;
        }
    }
}
