# Inhaltselement Seitenteaser
Die Erweiterung 'Seitenteaser' stellt Inhaltselemente bereit, die automatisch aus bestehenden Seiten Seitenteaser generieren. Als Bild dient das Seitenbild des Contao-Kerns (ab Contao 6, unter Contao 5.7 über heimseiten/contao-primary-page-image-bundle), ersatzweise das Seitenbild von terminal42/contao-pageimage und ansonsten das erste in der Seite verwendete Bild. Außerdem wird der Seitentitel und die Seitenbeschreibung verwendet. Dies kann überschrieben werden, wenn in der Seite Extraeingaben für den Teaser Titel und die Teaserbeschreibung gemacht werden. 

Im Startpunkt der Webseite kann eingestellt werden, welche Bildgröße verwendet und, ob ein Text angezeigt werden soll.

## Bildunterschrift

Optional kann die in den Metadaten der Bilddatei hinterlegte **Bildunterschrift** im Teaser
ausgegeben werden – als Overlay auf dem Bild (unten rechts, leicht transparent hinterlegt, nur so
breit wie der Text). Redakteure pflegen sie im Dateimanager; dieselbe Angabe wirkt damit überall
dort, wo das Bild verwendet wird. Nützlich z. B. für Hinweise wie „KI-generiert“.

Die Ausgabe ist **standardmäßig aus** und wird im Seitenlayout unter „Bildgrößen“ aktiviert:
**Bildunterschriften in den Teasern anzeigen**. Bestehende Installationen verhalten sich nach einem
Update also unverändert, auch wenn bei Bildern Bildunterschriften hinterlegt sind.

Anpassen über CSS-Variablen (auf `.ce_teaser_item` oder weiter oben gesetzt):

| Variable | Standard |
| --- | --- |
| `--teaser_caption_offset` | `10px` |
| `--teaser_caption_background` | `rgba(255, 255, 255, 0.72)` |
| `--teaser_caption_color` | `#444` |
| `--teaser_caption_font_size` | `13px` |
| `--teaser_caption_padding` | `3px 9px` |
| `--teaser_caption_radius` | `3px` |

Ausblenden: `.ce_teaser_item .teaser_caption { display: none; }`
