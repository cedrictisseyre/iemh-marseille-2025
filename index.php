

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
		ul.menu > li:hover, ul.sousmenu > li:hover { background: #d0e6f7; }
		ul.menu > li > .folder, ul.sousmenu > li > .folder { display: flex; align-items: center; padding: 12px 18px; cursor: pointer; font-size: 1.08em; font-weight: bold; }
		ul.menu > li > .folder .icon, ul.sousmenu > li > .folder .icon { margin-right: 8px; color: #217dbb; }
		ul.sousmenu { display: none; position: static; min-width: 220px; background: #f8fcff; border-radius: 0 0 8px 8px; box-shadow: 0 2px 8px #ccc; z-index: 10; padding: 8px 0 8px 24px; }
		ul.menu > li:hover > ul.sousmenu, ul.sousmenu > li:hover > ul.sousmenu { display: block; }
		ul.sousmenu li { margin: 0; padding: 0; }
		ul.sousmenu a { display: flex; align-items: center; padding: 8px 22px; color: #3498db; text-decoration: none; font-size: 1em; transition: background 0.2s, color 0.2s; border-radius: 4px; }
		ul.sousmenu a .icon { margin-right: 8px; color: #888; }
		ul.sousmenu a:hover { background: #eaf7ff; color: #217dbb; }
		.aucun { padding: 12px 18px; color: #888; font-style: italic; }
	</style>
</head>
<body>
	<div class="container">
		<h1>IEHM. Marseille, 2025</h1>
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
			foreach ($dossiers as $dossier) {
				echo "<li><span class='folder'><span class='icon'>📁</span>$dossier</span>";
				// Sous-menu pour le dossier
				echo "<ul class='sousmenu'>";
				explorer($chemin . '/' . $dossier, $niveau + 1);
				echo "</ul></li>";
			}
			foreach ($fichiers as $fichier) {
				$url = $chemin . '/' . $fichier;
				$ext = strtolower(pathinfo($fichier, PATHINFO_EXTENSION));
				$icon = ($ext === 'php') ? '🐘' : (($ext === 'html' || $ext === 'htm') ? '🌐' : (($ext === 'md' || $ext === 'readme') ? '📄' : '📄'));
				echo "<li><a href='./$url' target='_blank'><span class='icon'>$icon</span>$fichier</a></li>";
			}
		}
		// On liste uniquement les dossiers à la racine (sauf .git)
		foreach (scandir('.') as $item) {
			if ($item === '.' || $item === '..' || $item === '.git' || !is_dir($item)) continue;
			echo "<li><span class='folder'><span class='icon'>📁</span>$item</span>";
			echo "<ul class='sousmenu'>";
			explorer($item);
			echo "</ul></li>";
		}
		?>
		</ul>
	</div>
</body>
</html>
