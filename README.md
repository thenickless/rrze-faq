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
id="123456"
hideaccordeon="1"] 
```

Alle Attribute sind optional und können frei kombiniert werden mit folgenden Einschräkungen:
- mit dem Attribut id wird nur eine FAQ ausgegeben, alle weiteren Attribute sind überflüssig
- Attribut hideaccordeon wirkt nur in Kombination mit Attribut id


## Erklärungen zu den Attributen des Shortcodes

glossary : bestimmt, wonach gruppiert werden soll. Mögliche Werte sind "category" oder "tag". D.h. entweder es wird nach den Kategorien gruppiert oder nach Schlagwörtern. Um gar nicht zu gruppieren, reicht es, das Attribut glossary wegzulassen.

category : mit diesem Attribut wird bestimmt, zu welchen Kategorien passende FAQ ausgegeben werden sollen. Es können beliebig viele Kategorien angegeben werden. Nutzen Sie dazu die Titelform der Kategorien, die Sie im Menü unter "FAQ"->"Kategorie" finden und trennen Sie diese voneinander durch Kommata.

tag : mit diesem Attribut wird bestimmt, zu welchen Schlagwörtern passende FAQ ausgegeben werden sollen. Es können beliebig viele Schlagwörter angegeben werden. Nutzen Sie dazu die Titelform der Schlagwörter, die Sie im Menü unter "FAQ"->"Schlagwörter" finden und trennen Sie diese voneinander durch Kommata.

glossarystyle : dieses Attribut bestimmt, wie das Glossar oberhalb der FAQ aussehen soll. 
Mögliche Werte sind 
 - "a-z" für ein alphabetisches Register von A bis Z
 - "tagcloud" um die Begriffe, nach denen gruppiert wird anhand der Anzahl an gefundenen Treffer unterschiedlich groß darzustellen. Gruppiert wird nach Kategorien oder nach Schlagwörtern. Siehe "glossary". Aus Gründen der Abwärtskompatibilität ist es nötig, dass Sie glossarystyle="" verwenden müssen, wenn Sie kein Glossar anzeigen lassen möchten.

 color : bestimmt, in welcher Farbe die Accordeons erscheinen sollen. Verweden Sie dazu die Kennungen der Fakultäten: medfak oder natfak oder rwfak oder philfak oder techfak

 id : mit diesem Attribut erfolgt die Ausgabe einer einzigen FAQ, sofern diese vorhanden ist. Sie finden die ID in der rechten Spalte unter "FAQ"->"Alle FAQ" sowie in der Informationsbox "Einfügen in Seiten und Beiträgen" bei jeder FAQ im Bearbeitungsmodus.

 hideaccordeon : ist das Attribut "id" gesetzt, können Sie mit hideaccordeon bestimmen, dass die Ausgabe nicht in einem Accordeon erfolgen soll.



## FAQ von anderer Domain

Hierzu muss die gewünschte Domain über den Menüpunkt "Einstellungen" -> "RRZE FAQ" -> Tab "Domains" hinzugefügt werden.
Das Synchronisieren kann über den Menüpunkt "Einstellungen" -> "RRZE FAQ" -> Tab "Synchonisierung" vorgenommen werden.
Synchronisierte FAQ können nun wie selbst erstellte FAQ mit dem Shortcode ausgegeben werden.




