# RRZE-FAQ
WordPress-Plugin: Shortcode zur Einbindung von eigenen FAQ's sowie von FAQ-Einträgen aus dem FAU-Netzwerk. 
Hierbei wurden die Funktionen vom früheren Glossar übernommen und erweitert.

## Allgemeins

Die Möglichkeit eigene FAQ's (Glossare) zu definieren ist nun in ein eigenes Plugin ausgelagert worden.
Weiterhin können Glossare wie gewohnt im Backend unter dem Menüpunkt FAQ's angelegt werden.
Darüber hinaus können nun auch FAQ's von anderen Domains eingebunden werden.

## Verwendung des Shortcodes (wie bisher)

```html
[glossary id="1156754"] 
[glossary category="RW"]

[faq id="1156754"] 
[faq category="RW"]  
```

## Erweiterung des Shortcodes (FAQ`s von anderen Domains)

Um diesen Dienst zu verwenden muss die gewünschte Domain hinzugefügt werden. __(Menüpunkt Neue Domain hinzufügen)__.
Danach wird im Backend automatisch eine Liste der vorhandenen Einträge (FAQ's) dieser Domain erstellt. __(Menüpunkt Remote FAQ's)__
Dieser Dienst ist nur möglich, wenn diese Domain das FAQ-Plugin aktiviert hat.

Der Shortcode wurde um den Paramter rest und domain erweitert.<br/>
Die Domain finden Sie unter dem Menüpunkt - __Domains anzeigen__.

Die Kategorie und die jeweilige Id des FAQ's können Sie der Liste unter dem Menüpunkt - __Remote FAQ's__ - entnehmen.

```html
[faq id="2215763" domain="site1.wordpress.dev" rest="1" color="med"] 
[faq category="personen" domain="site1.wordpress.dev" rest="1"] 
```

