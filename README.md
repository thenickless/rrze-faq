# RRZE-FAQ

Das Plugin erlaubt das Anlegen von FAQ- oder Glossar-Einträgen. Diese können einzelnt, 
via Kategorien und als ganzes angezeigt werden.  Darüber hinaus können FAQ-EInträge 
andere Domains mit den selben PLugins übernommen und angezeogt werden.


## Download 

GITHub-Repo: https://github.com/RRZE-Webteam/rrze-faq/


## Autor 
RRZE-Webteam , http://www.rrze.fau.de

## Copryright

GNU General Public License (GPL) Version 2 



## Verwendung des Shortcodes


### Aufruf aller FAQ-Einträge:

```html
[faq]  
```


### Aufruf einer Kategorie von FAQ-Einträgen

```html 
[faq category="$Kategorieslug"]  
```

### Aufruf eines einzelnen FAQ-Eintrags

```html 
[faq id="$FAQ-ID"]  
```


Der bisher bei den FAU Themes verwendete Shortcode [glossary] wird durch das Plugin ebenfalls unterstützt.


## Erweiterung des Shortcodes (FAQ`s von anderen Domains)

Um diesen Dienst zu verwenden muss die gewünschte Domain hinzugefügt werden. __(Menüpunkt Neue Domain hinzufügen)__.
Danach wird im Backend automatisch eine Liste der vorhandenen Einträge (FAQ's) dieser Domain erstellt. __(Menüpunkt Remote FAQ's)__
Dieser Dienst ist nur möglich, wenn diese Domain das FAQ-Plugin aktiviert hat.

Der Shortcode wurde um den Paramter rest und domain erweitert.<br/>
Die Domain finden Sie unter dem Menüpunkt - __Domains anzeigen__.

Die Kategorie und die jeweilige Id des FAQ's können Sie der Liste unter dem Menüpunkt - __Remote FAQ's__ - entnehmen.


### Ausgabe aller FAQs einer entfernten Domain

```html
[faq  domain="www.wordpress.dev" ] 
```

Es wird per Default stets angenommen, daß die API via SSL erreichbar ist. Sollte eine Domain nicht via SSL erreichbar sein, ist das 
Protokoll mit anzugeben:

```html
[faq  domain="http://plain.wordpress.dev" ] 
```

### Ausgabe der FAQs einer entfernten Domainmit einer Kategorie

```html
[faq category="personen" domain="site1.wordpress.dev" ] 
```

### Ausgabe einer speziellen FAQs einer entfernten Domainmit über ihre ID
```html
[faq id="2215763" domain="site1.wordpress.dev"] 
```

