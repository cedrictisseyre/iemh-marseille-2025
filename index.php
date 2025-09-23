

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>IEHM. Marseille, 2025</title>
	<style>
		body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
		.container { max-width: 800px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
		h1 { color: #2c3e50; }
		ul.menu { list-style: none; padding: 0; }
		ul.menu > li { position: relative; margin: 12px 0; background: #eaf7ff; border-radius: 6px; box-shadow: 0 1px 4px #d0e6f7; transition: background 0.2s; }
		ul.menu > li:hover { background: #d0e6f7; }
		ul.menu > li > strong { display: block; padding: 12px 18px; cursor: pointer; font-size: 1.08em; }
		ul.sousmenu { display: none; position: absolute; left: 0; top: 100%; min-width: 220px; background: #fff; border-radius: 0 0 8px 8px; box-shadow: 0 2px 8px #ccc; z-index: 10; padding: 10px 0; }
		ul.menu > li:hover > ul.sousmenu { display: block; }
		ul.sousmenu li { margin: 0; padding: 0; }
		ul.sousmenu a { display: block; padding: 10px 22px; color: #3498db; text-decoration: none; font-size: 1em; transition: background 0.2s, color 0.2s; }
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
		$dossiers = array_filter(glob('*'), 'is_dir');
		foreach ($dossiers as $dossier) {
			$liens = [];
			// Fichiers à la racine du dossier
			foreach (glob("$dossier/*.{php,html,md,MD,readme,README}", GLOB_BRACE) as $fichier) {
				$nom = basename($fichier);
				$extension = pathinfo($fichier, PATHINFO_EXTENSION);
				if ($extension === 'php') {
					$liens[] = "<li><a href='./$fichier' target='_blank'>$nom</a></li>";
				} else {
					$liens[] = "<li><a href='./$fichier' target='_blank'>$nom</a></li>";
				}
			}
			// Fichiers dans les sous-dossiers
			foreach (glob("$dossier/**/*.{php,html,md,MD,readme,README}", GLOB_BRACE) as $fichier) {
				$nom = str_replace($dossier . '/', '', $fichier);
				$extension = pathinfo($fichier, PATHINFO_EXTENSION);
				if ($extension === 'php') {
					$liens[] = "<li><a href='./$fichier' target='_blank'>$nom</a></li>";
				} else {
					$liens[] = "<li><a href='./$fichier' target='_blank'>$nom</a></li>";
				}
			}
			if (!empty($liens)) {
				echo "<li><strong>$dossier</strong><ul class='sousmenu'>" . implode('', $liens) . "</ul></li>";
			} else {
				echo "<li><strong>$dossier</strong><span class='aucun'>Aucun fichier .php ou .html trouvé</span></li>";
			}
		}
		?>
		</ul>
	</div>
</body>
</html>
