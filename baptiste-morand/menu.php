<!-- menu.php -->
<style>
.menu-burger-container {
  position: absolute;
  top: 30px;
  left: 30px;
  z-index: 100;
}
.menu-burger {
  width: 40px;
  height: 40px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  cursor: pointer;
}
.menu-burger span {
  display: block;
  width: 30px;
  height: 4px;
  margin: 4px 0;
  background: #2c3e50;
  border-radius: 2px;
  transition: 0.3s;
}
.menu-links {
  display: none;
  position: absolute;
  top: 50px;
  left: 0;
  background: #fff;
  border: 1px solid #2c3e50;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(44,62,80,0.15);
  padding: 10px 20px;
}
.menu-links a {
  display: block;
  color: #2c3e50;
  text-decoration: none;
  padding: 8px 0;
  font-size: 1.1em;
}
.menu-links a:hover {
  background: #ecf0f1;
}
</style>
<div class="menu-burger-container">
  <div class="menu-burger" onclick="document.getElementById('menu-links').style.display = (document.getElementById('menu-links').style.display === 'block' ? 'none' : 'block');">
    <span></span>
    <span></span>
    <span></span>
  </div>
  <div class="menu-links" id="menu-links">
    <a href="index.php">Accueil</a>
    <a href="ajout_complet.php">Ajout Complet</a>
    <a href="joueurs/liste.php">Liste Joueurs</a>
    <a href="joueurs/stats.php">Stats Joueurs</a>
    <a href="matchs/liste.php">Liste Matchs</a>
    <a href="matchs/stats.php">Stats Matchs</a>
    <a href="saisons/liste.php">Liste Saisons</a>
  </div>
</div>
