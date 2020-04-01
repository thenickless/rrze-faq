# RRZE-FAQ
WordPress-Plugin: Shortcode zur Einbindung von eigenen FAQ, von OTRS synchronisierten, sowie von FAQ-Einträgen aus dem FAU-Netzwerk. 

## Allgemeines

Das Plugin kann genutzt werden, um FAQ von OTRS zu speichern, FAQ zu erstellen und FAQ von Websites aus dem FAU-Netzwerk einzubinden. Es kann nach Kategorien und Schlagwörtern gefiltert werden. Das Layout lässt sich derart bestimmen, dass ein A-Z Register, die Kategorien bzw Schlagwörter als Links oder als Links, die sich nach Anzahl der gefundenen Treffer in der Größe unterscheiden ausgebeben werden kann. Kategorien und Schlagwörter werden in Akkordeons gruppiert. Es ist ebenso möglich, eine einzelne FAQ auszugeben.

## Verwendung des Shortcodes

```html
[faq id=123456"] 
[faq category="titelform-der-kategorie, titelform-einer-weiteren-kategorie"]
[faq tag="titelform-des-schlagworts, titelform-eines-weiteren-schlagworts"]
[faq category="titelform-der-kategorie, titelform-einer-weiteren-kategorie"
 tag="titelform-des-schlagworts, titelform-eines-weiteren-schlagworts"]
```

## Alle Attribute des Shortcodes

```html
[faq 
domain="kurzbezeichnung" 
glossary="category oder tag" 
category="beliebig-viele-kategorien-kommagetrennt"  
category="beliebig-viele-schlagwoerter-kommagetrennt" 
glossarystyle="a-z oder tagcloud oder tabs" 
color="medfak oder natfak oder rwfak oder philfak oder techfak" 
id="123456"] 
```

Alle Attribute sind optional und können frei kombiniert werden mit folgenden Einschräkungen:
- mit dem Attribut id wird nur eine FAQ ausgegeben, alle weiteren Attribute sind überflüssig
- ist domain gesetzt, kann category nicht mit glossary="tag" bzw tag mit glossary="category" verwendet werden (dies wird noch gelöst werden)

## FAQ von ORTS

Unter "Einstellungen" -> "RRZE FAQ" -> Tab "OTRS" können die Kategorien ausgewählt werden, die synchronisiert werden sollen. Dauert die Synchronisierung länger, als der Server Skripte ausführen lässt, erscheint eine Meldung, die nächste Synchronisierung manuell anzustoßen. Die Synchronisierung kann auch automatisch erfolgen in folgenden Abständen: Wochentags, tagsüber 8-18 Uhr alle 3 Stunden, danach und am Wochenende alle 6 Stunden.


## FAQ von anderen Domain

Hierzu muss die gewünschte Domain über den Menüpunkt "Einstellungen" -> "RRZE FAQ" -> Tab "Domains" hinzugefügt werden.


### Ausgabe aller FAQ einer entfernten Domain

```html
[faq  domain="kurzbezeichnung" ] 
```

### Ausgabe der FAQ einer entfernten Domain mit einer Kategorie

```html
[faq  domain="kurzbezeichnung" category="titelform-der-kategorie"] 
```

### Ausgabe der FAQ einer entfernten Domain mit Schlagwörtern

```html
[faq  domain="kurzbezeichnung" tag="titelform-des-schlagworts, titelform-eines-weiteren-schlagworts"] 
```

### Ausgabe einer speziellen FAQ einer entfernten Domain über ihre ID
```html
[faq  domain="kurzbezeichnung" id="123456"] 
```

