=== RRZE FAQ ===
Contributors: rrze-webteam
Tags: faq, shortcode, block, widget, categories
Requires at least: 6.1
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: 5.3.18
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin zur Erstellung und Synchronisierung von FAQ innerhalb des FAU-Netzwerks. Verwendbar als Shortcode, Block oder Widget.

== Beschreibung ==

Das Plugin ermöglicht die Erstellung von FAQ und die Synchronisation mit anderen FAU-Websites.

Funktionen:
* Ausgabe der FAQ nach Kategorie, Schlagwort oder Domain
* Gruppierung als Akkordeon oder Liste
* Glossar als A-Z-Register, Tabs oder Tagcloud
* REST-API-Unterstützung
* Unterstützung mehrerer Domains via Synchronisierung
* Widget zur Anzeige eines zufälligen oder festen FAQ
* Shortcodes mit umfangreichen Attributen
* Unterstützung für Gutenberg-Block

== Installation ==

1. Lade das Plugin herunter.
2. Entpacke die ZIP-Datei.
3. Lade den Ordner `rrze-faq` in das Verzeichnis `/wp-content/plugins/` deiner WordPress-Installation.
4. Aktiviere das Plugin über das Menü `Plugins` in WordPress.
5. Optional: Konfiguriere Synchronisations-Domains unter `Einstellungen > RRZE FAQ`.

== Verwendung ==

Beispiele für Shortcodes:

    [faq]
    [faq category="kategorie-1"]
    [faq tag="schlagwort-1"]
    [faq id="123, 456"]
    [faq glossary="category tabs"]
    [faq glossary="tag tagcloud" show="expand-all-link"]

Weitere Details zur Nutzung finden sich in der Datei `readme.md` oder in der Dokumentation.

== Frequently Asked Questions ==

= Kann ich FAQ von anderen FAU-Websites anzeigen? =
Ja. Dazu muss die Domain unter `Einstellungen > RRZE FAQ > Domains` hinzugefügt und die Synchronisation ausgeführt werden.

= Gibt es ein Widget? =
Ja. Es ist unter `Design > Widgets` als „FAQ Widget“ verfügbar.

= Wie funktioniert die REST-API? =
Das Plugin unterstützt die WordPress REST-API v2 mit erweiterten Filtermöglichkeiten.

== Screenshots ==

1. FAQ in Akkordeon-Darstellung
2. A-Z Glossar
3. Widget-Einstellungen

== Changelog ==

= 5.3.18 =
* Diverse Fehlerbehebungen
* Verbesserte Kompatibilität mit WordPress 6.7

== Upgrade Notice ==

= 5.3.18 =
Diese Version behebt kleinere Darstellungsfehler und verbessert die API-Kompatibilität.

== License ==

Dieses Plugin ist freie Software unter GPLv2 oder später.
