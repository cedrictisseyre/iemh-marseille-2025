<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une discipline</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 400px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; text-align: center; }
        label, input { display: block; width: 100%; margin-bottom: 10px; }
        input[type="submit"] { background: #2980b9; color: #fff; border: none; padding: 10px; border-radius: 4px; cursor: pointer; }
        .retour { display: block; text-align: center; margin: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ajouter une discipline</h1>
        <form method="post" action="../services/ajouter_discipline.php">
            <label for="nom">Nom de la discipline :</label>
            <input type="text" name="nom" id="nom" required>
            <input type="submit" value="Ajouter">
        </form>
        <a class="retour" href="../page.php">Retour à l'accueil</a>
    </div>
</body>
</html>
