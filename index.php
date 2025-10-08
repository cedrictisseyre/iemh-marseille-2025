

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>IEHM. Marseille, 2025</title>
	<style>
		body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
		.container { max-width: 900px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 12px #b0c4de; }
		h1 { color: #2c3e50; }
		ul.menu, ul.sousmenu { list-style: none; padding-left: 0; margin: 0; }
		ul.menu > li, ul.sousmenu > li { position: relative; margin: 8px 0; background: #eaf7ff; border-radius: 6px; box-shadow: 0 1px 4px #d0e6f7; transition: background 0.2s; }
		ul.menu > li { margin-left: 48px; } /* Indentation plus large pour le premier niveau */
		ul.menu > li:hover, ul.sousmenu > li:hover { background: #d0e6f7; }
		ul.menu > li > .folder, ul.sousmenu > li > .folder { display: flex; align-items: center; padding: 12px 18px; cursor: pointer; font-size: 1.08em; font-weight: bold; }
		ul.menu > li > .folder .icon, ul.sousmenu > li > .folder .icon { margin-right: 8px; color: #217dbb; }
		ul.sousmenu {
			display: none;
			position: static;
			min-width: 220px;
			background: #f8fcff;
			border-radius: 0 0 8px 8px;
			box-shadow: 0 2px 8px #ccc;
			z-index: 10;
			padding: 8px 0 8px 24px;
			max-height: 0;
			overflow: hidden;
			transition: max-height 0.6s cubic-bezier(.4,0,.2,1), padding 0.6s cubic-bezier(.4,0,.2,1);
		}
		li.open > ul.sousmenu {
			display: block;
			max-height: 1000px;
			padding: 8px 0 8px 24px;
		}
		ul.sousmenu li { margin: 0; padding: 0; }
		ul.sousmenu a { display: flex; align-items: center; padding: 8px 22px; color: #3498db; text-decoration: none; font-size: 1em; transition: background 0.2s, color 0.2s; border-radius: 4px; }
		ul.sousmenu a .icon { margin-right: 8px; color: #888; }
		ul.sousmenu a:hover { background: #eaf7ff; color: #217dbb; }
		.aucun { padding: 12px 18px; color: #888; font-style: italic; }
	</style>
</head>
<body>
<?php require_once 'evaluation_etudiant.php'; ?>
	<div class="container">
		<h1>IEHM. Marseille, 2025</h1>
		<div style="background:#f8fcff;border-radius:8px;padding:18px 24px;margin-bottom:24px;box-shadow:0 1px 6px #d0e6f7;">
			<h2 style="margin-top:0;color:#217dbb;font-size:1.15em;">Critères d'évaluation automatisés indicatifs (Ce n'est pas une note)</h2>
			<ul style="margin:0 0 8px 0;padding-left:18px;">
				<li>Nombre de commits (activité et régularité)</li>
				<li>Présence d’un README ou d’une documentation</li>
				   <li>Arborescence et fichiers (organisation des dossiers et fichiers)</li>
				<li>Respect des bonnes pratiques (noms, indentation, commentaires)</li>
				<li>Fonctionnalité des scripts (exécution sans erreur)</li>
				<li>Utilisation de la base de données (requêtes, structure, interactions)</li>
			</ul>
			<div style="color:#e67e22;font-size:1.05em;margin-top:8px;">Avancement intermédiaire : <strong>2 TD restants</strong> (la note maximale actuelle est limitée)</div>
		</div>
		<p>Bienvenue ! Voici la liste des dossiers du projet :</p>
		<ul class="menu">
		<?php


	function explorer($chemin, $niveau = 0) {
			$contenu = scandir($chemin);
			$dossiers = [];
			$fichiers = [];
			foreach ($contenu as $item) {
				if ($item === '.' || $item === '..') continue;
				$fullpath = $chemin . '/' . $item;
				if (is_dir($fullpath)) {
					$dossiers[] = $item;
				} else {
					$fichiers[] = $item;
				}
			}
			// Indentation responsive en fonction de la largeur de l'écran
			$baseIndent = 2; // pourcentage réduit
			$indent = ($baseIndent * ($niveau + 1)) . 'vw';
			foreach ($dossiers as $dossier) {
				echo "<li style='margin-left: {$indent}'><span class='folder'><span class='icon'>📁</span>$dossier</span>";
				// Sous-menu pour le dossier
				echo "<ul class='sousmenu'>";
				explorer($chemin . '/' . $dossier, $niveau + 1);
				echo "</ul></li>";
			}
			foreach ($fichiers as $fichier) {
				$url = $chemin . '/' . $fichier;
				$ext = strtolower(pathinfo($fichier, PATHINFO_EXTENSION));
				$icon = ($ext === 'php') ? '🐘' : (($ext === 'html' || $ext === 'htm') ? '🌐' : (($ext === 'md' || $ext === 'readme') ? '📄' : '📄'));
				echo "<li style='margin-left: {$indent}'><a href='./$url' target='_blank'><span class='icon'>$icon</span>$fichier</a></li>";
			}
		}
		$maxProgress = 14; // plafond dynamique, par exemple 14/20 si 2 TD restants
		foreach (scandir('.') as $item) {
			if ($item === '.' || $item === '..' || $item === '.git' || !is_dir($item)) continue;
			   $globalScore = getGlobalScore($item); // sur 100
			   $color = $globalScore < 50 ? '#e74c3c' : ($globalScore < 80 ? '#f39c12' : '#27ae60');
			   $progressBar = "<div class='progress-bar' style='width:120px;height:16px;background:#eee;border-radius:8px;display:inline-block;margin-left:12px;vertical-align:middle;'><div class='progress' style='height:100%;width:" . ($globalScore) . "%;;background:$color;border-radius:8px;transition:width 0.5s;'></div></div> <span style='font-size:0.95em;color:$color;'>$globalScore/100</span>";

			   // Utilisation des autres fonctions d'évaluation
			   $commits = getCommitCount($item);
			   $readme = hasReadme($item) ? "<span style='color:#27ae60'>README présent</span>" : "<span style='color:#e74c3c'>README absent</span>";
			   $arboFiles = getFileTreeAndFilesScore($item);
			   $bestPractices = getBestPracticesScore($item);
			   $functionality = getScriptFunctionalityScore($item);
			   $dbUsage = getDatabaseUsageScore($item);

			   $indicateurs = "<div style='font-size:0.95em;color:#555;margin-top:4px;'>"
				   . "Commits : $commits | $readme | Arborescence et fichiers : $arboFiles/10 | Pratiques : $bestPractices/10 | Fonctionnalité : $functionality/10 | BDD : $dbUsage/10"
				   . "</div>";

			   echo "<li><span class='folder'><span class='icon'>📁</span>$item</span> $progressBar $indicateurs";
			   echo "<ul class='sousmenu'>";
			   explorer($item);
			   echo "</ul></li>";
		}
		?>
		</ul>
		<script>
		// JS pour ouvrir/fermer les sous-menus au clic
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.folder').forEach(function(folder) {
				folder.addEventListener('click', function(e) {
					e.stopPropagation();
					var li = folder.parentElement;
					var wasOpen = li.classList.contains('open');
					// Ferme tous les autres sous-menus au même niveau
					if (li.parentElement) {
						li.parentElement.querySelectorAll('li.open').forEach(function(openLi) {
							openLi.classList.remove('open');
						});
					}
					// Ouvre ou ferme le menu cliqué
					if (!wasOpen) {
						li.classList.add('open');
					} else {
						li.classList.remove('open');
					}
				});
			});
		});
		</script>
	</div>
</body>
</html>
