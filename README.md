# RRZE-FAQ
WordPress-Plugin: Shortcode zur Einbindung von eigenen FAQ sowie von FAQ-Einträgen aus dem FAU-Netzwerk. 

## Allgemeines

Das Plugin kann genutzt werden, um FAQ zu erstellen und FAQ von Websites aus dem FAU-Netzwerk zu synchronisieren. Es kann nach Kategorien und Schlagwörtern gefiltert werden. Das Layout lässt sich derart bestimmen, dass ein A-Z Register, die Kategorien bzw Schlagwörter als Links oder als Links, die sich nach Anzahl der gefundenen Treffer in der Größe unterscheiden ausgebeben werden kann. Kategorien und Schlagwörter werden in Akkordeons gruppiert. Es ist ebenso möglich, eine einzelne FAQ auszugeben.

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
glossary="category oder tag" 
category="beliebig-viele-kategorien-kommagetrennt"  
tag="beliebig-viele-schlagwoerter-kommagetrennt" 
glossarystyle="a-z oder tagcloud oder tabs" 
color="medfak oder natfak oder rwfak oder philfak oder techfak" 
id="123456"] 
```

Alle Attribute sind optional und können frei kombiniert werden mit folgender Einschräkung:
- mit dem Attribut id wird nur eine FAQ ausgegeben, alle weiteren Attribute sind überflüssig


## FAQ von anderer Domain

Hierzu muss die gewünschte Domain über den Menüpunkt "Einstellungen" -> "RRZE FAQ" -> Tab "Domains" hinzugefügt werden.
Das Synchronisieren kann über den Menüpunkt "Einstellungen" -> "RRZE FAQ" -> Tab "Synchonisierung" vorgenommen werden.
Synchronisierte FAQ können nun wie selbst erstellte FAQ mit dem Shortcode ausgegeben werden.




