<?php
include "sessionsStart.php";
include "header.php";
include "connect.php";
?>
<body>

<?php include "inc/nav.php" ?>

<div class="container">
	<h1>Studi-Recommender</h1>
	<p>
		Der Studienführer bietet zwei Arten von Empfehlungen an.
		Einerseits empfielt er jedem Nutzer auf der Startseite individuell, welche Veranstaltungen zu ihm passen könnten.
		Anderseits empfielt er auf Veranstaltungsseiten weitere Veranstaltungen, die zu dieser passen könnten.
		Hier erfährst du mehr dazu.
	</p>
	<br>
	<h2>FAQs</h2>
	<h3>Warum werden mir keine Veranstaltungsbewertungen angezeigt?</h3>
	<p>
		Damit wir dir vernünftige Empfehlungen anzeigen können, musst du mindestens drei Bewertungen abgegeben haben - andernfalls wissen wir einfach zu wenig über deine Präferenzen.
		Umso mehr Veranstaltungen du bewertest (und auch umso mehr alle Nutzer des Studienführers bewerten), desto besser werden deine Empfehlungen.
	</p>
	<h3>Warum werden meine Veranstaltungsbewertungen nicht direkt im Recommender berücksichtigt?</h3>
	<p>
		Der Studi-Recommender wurde in Python mit dem <a href="https://making.lyst.com/lightfm/docs/home.html">LightFM-Package</a> implementiert.
		Da unsere Server derzeit kein Python unterstützen, wird regelmäßig ein Abzug der Studienführer-Datenbank gemacht, um die Empfehlungen anderswo zu generieren und dann in der Studienführer-Datenbank abzulegen.
		Da die Empfehlungen also nicht in Echtzeit entstehen, werden Veranstaltungsbewertungen bis zu diesem Update nicht berücksichtigt.
	</p>
	<h3>Warum werden meine Empfehlungen umso besser je mehr Bewertungen ich abgebe?</h3>
	<p>
		Grob gesagt sucht ein Algorithmus basierend auf deinen besuchten Veranstaltungen nach passenden Empfehlungen.
		Umso mehr der Algorithmus zum Arbeiten hat, desto besser werden die Empfehlungen.
		Weitere Details findest du weiter unten.
		Übrigens werden aus diesem Grund die Empfehlungen auf den Veranstaltungsseiten auch besser je öfter diese Veranstaltung bewertet wird - am besten von Nutzern mit vielen anderen Bewertungen.
	</p>
	<br>
	<h2>Unter der Haube: So werden die Empfehlungen erstellt</h2>
	<h3>Kollaboratives Filtern</h3>
	<p>
		Grundsetzlich basieren alle Empfehlungen auf den Interaktionen zwischen Nutzern und Veranstaltungen.
		Wir checken also, welcher Nutzer, welche Veranstaltung besucht hat.
		Das lässt sich gut in einer Interaktionsmatrix darstellen:		
	</p>
	<img src="pictures/recommender/rec_interaktionsmatrix.png" style="max-width:100%; padding:5%">
	<p>
		Dabei drückt eine 1 aus, dass der jeweilige Nutzer die jeweilige Veranstaltung besucht hat.
		Ein Fragezeichen drückt aus, dass der jeweilige Nutzer, die jeweilige Veranstaltung nicht besucht hat - außerdem wissen wir erstmal nicht, ob dieser Nutzer diese Veranstaltung besuchen würde.
	</p>
	<p>
		Um eine Veranstaltungempfehlung für einen Nutzer A zu generieren, suchen wir nach <i>ähnlichen</i> Nutzern; also Nutzern, die auch Veranstaltungen besucht haben, die Nutzer A besucht hat.
		Haben wir so einen Nutzer B gefunden, prüfen wir, ob dieser Nutzer B darüber hinaus weitere Veranstaltungen besucht hat - und empfehlen diese Nutzer A.
		Umso mehr Veranstaltungen ein Nutzer bewertet hat, umso besser funktioniert dieser Schritt.
	</p>
	<img src="pictures/recommender/rec_kollaborativesFiltern.png" style="max-width:100%; padding:5%">
	<h3>Matrixfaktorisierung</h3>
	<p>
		Alleine mit Kollaborativem Filtern können bereits gute Empfehlungen generiert werden.
		Wir gehen allerdings noch einen Schritt weiter und nutzen die Matrixfaktorisierung.
		Dabei wird die Interaktionsmatrix in zwei niedrigdimensionale Matrizen unterteilt, wobei die beiden neuen Matrizen miteinander multipliziert wieder die Interaktionsmatrix ergeben.
		Von diesen neuen Matrizen repräsentiert eine die Nutzer, die andere die Veranstaltungen.
	</p>
	<p>
		Jede Dimension dieser Matrizen stellt einen latenten Faktor dar, anhand welcher Nutzer und Schulungen eingeteilt werden.
		Einer dieser latenten Faktoren könnte beispielsweise sein, wie sehr es um anwendungsbezogenes Wissen (im Gegensatz zu Grundlagenwissen) geht.
		Ein anderer laterter Faktor könnte sein, wie sehr es um Praxiswissen (im Gegensatz zu theoretischem Wissen) geht.
		Jeder Nutzer und jede Veranstaltung wird dann anhand dieser latenten Faktoren eingeteilt.
		Diese Faktoren wollen wir aber nicht vorgeben, sondern lassen einen cleveren Algorithmus die Arbeit übernehmen.
		Es ist aber nicht unüblich, dass die Faktoren im Nachhinein interpretiert werden können.
		Dafür schaut man sich beispielweise alle Veranstaltungen an, die bei dem zu interpretierenden Faktor sehr hohe (oder sehr niedrige) Werte haben und überprüft, was sie gemeinsam haben.
	</p>
	<img src="pictures/recommender/rec_matrixfaktorisierung.png" style="max-width:100%; padding:5%">
	<p>
		Um nun Empfehlungen zu generieren, schauen wir uns an, welche Nutzer und Veranstaltungen ähnliche Ausprägungen der Faktoren haben.
		Ein Nutzer hat beispielsweise einen hohen Wert bei Faktor 1 und einen niedrigen Wert bei Faktor 2.
		Das gleiche gilt für eine bestimmte Veranstaltung, sodass diese Veranstaltung wahrscheinlich gut zu diesem Nutzer passt.
		Alle latenten Faktoren spannen den latenten Raum auf - jeder Faktor entspricht einer Dimension.
		Nutzer und Veranstaltungen, die sich nahe in diesem Raum aufhalten, passen vermutlich gut zusammen.
		Gleiches gilt auch für zwei Veranstaltungen oder zwei Nutzer, die nahe zusammen liegen.
	</p>
	<img src="pictures/recommender/rec_latenterRaum.png" style="max-width:100%; padding:5%">
	<p>
		Da nun jeder Nutzer und jede Veranstaltung durch einen Vektor (aus allen Werten der latenten Faktoren) dargestellt werden kann, kann einfach die Kosinus-Distanz berechnet werden, die angibt, wie nahe sich diese Elemente befinden.
		Das geht auch bei höheren Dismensionen sehr einfach.
	</p>
	<h3>Filtern</h3>
	<p>
		Die fertige Empfehlungsliste kann noch gefiltert werden.
		Der Studienführer filtert alle Veranstaltungen heraus, die der jeweilige Nutzer bereits besucht hat.
		Das gilt sowohl für die idividuellen Filter auf der Startseite, als auch für die Veranstaltungsvorschläge auf der Veranstaltungsseite.
		Darüber hinaus löschen wir aus der fertigen Empfehlungsliste alle Empfehlungen für Nutzer oder Veranstaltungen mit weniger als drei Bewertungen (da diese wahrscheinlich sowieso unbrauchbar sind).
		Weitere Filter sind derzeit nicht aktiv.
	</p>
</div>

</body>