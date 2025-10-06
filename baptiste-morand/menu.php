<!-- menu.php -->
<style>
.menu-deroulant {
  width:100%;
  display:flex;
  justify-content:center;
  margin-top:10px;
}
.menu-deroulant select {
  font-size:1.1em;
  padding:8px 16px;
  border-radius:5px;
  border:1px solid #2c3e50;
  background:#ecf0f1;
  color:#2c3e50;
}
</style>
<div class="menu-deroulant">
  <select onchange="if(this.value) window.location.href=this.value;">
    <option value="">-- Navigation --</option>
    <option value="index.php">Accueil</option>
    <option value="ajout_complet.php">Ajout Complet</option>
    <option value="joueurs/liste.php">Liste Joueurs</option>
    <option value="joueurs/stats.php">Stats Joueurs</option>
    <option value="matchs/liste.php">Liste Matchs</option>
    <option value="matchs/stats.php">Stats Matchs</option>
    <option value="saisons/liste.php">Liste Saisons</option>
  </select>
</div>
