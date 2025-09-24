<?php
date_default_timezone_set('Europe/Paris');

// Date du combat : 29 octobre 2025 à 03h00 du matin
$eventDate = new DateTime("2025-10-29 03:00:00");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>UFC 321 - Gane vs Aspinall</title>
  <style>
    /* --- GLOBAL --- */
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, Helvetica, sans-serif;
      background: #0a0a0a;
      color: #fff;
      text-align: center;
    }

    h1, h2, h3 {
      margin: 0;
      padding: 0;
    }

    /* --- HEADER --- */
    header {
      padding: 20px;
    }

    header img {
      height: 100px;
    }

    header h2 {
      margin-top: 10px;
      font-size: 1.5rem;
      font-weight: normal;
      color: #ffd700;
    }

    /* --- MAIN CONTAINER --- */
    .fight-card {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 20px;
    }

    /* --- FIGHTERS --- */
    .fighter {
      width: 30%;
      text-align: center;
    }

    /* Aspinall légèrement plus grand que Gane */
    .fighter.aspinall img {
      width: 95%; /* boosté pour compenser */
      max-width: 340px;
    }

    .fighter.gane img {
      width: 85%;
      max-width: 320px;
    }

    .fighter h2 {
      font-size: 1.8rem;
      margin-top: 10px;
      font-weight: bold;
      color: #ffffff;
    }

    .fighter p {
      font-size: 1.1rem;
      color: #d9d9d9;
    }

    /* --- COMPARATIF --- */
    .stats {
      width: 30%;
      background: rgba(0, 0, 0, 0.8);
      border: 2px solid #d4af37; /* or */
      border-radius: 15px;
      padding: 20px;
    }

    .stats h3 {
      font-size: 1.5rem;
      margin-bottom: 20px;
      color: #d4af37; /* doré */
      text-transform: uppercase;
    }

    .stats table {
      width: 100%;
      border-collapse: collapse;
      font-size: 1.1rem;
      color: #fff;
    }

    .stats td {
      padding: 10px;
      border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    .stats td:first-child {
      color: #d4af37; /* or pour la légende */
      font-weight: bold;
      text-align: left;
    }

    .stats td:not(:first-child) {
      text-align: center;
    }

    /* --- FOOTER TEXT --- */
    .footer-text {
      margin-top: 20px;
      font-size: 1rem;
      color: #ccc;
    }
  </style>
</head>
<body>

  <!-- LOGO UFC -->
  <header>
    <img src="https://apinew.goalzo.co.il/admin/images/teams-logos/ufc%20321.png" alt="UFC Logo">
    <h2>29 octobre 2025 à 3h (heure française)</h2>
  </header>

  <!-- FIGHT CARD -->
  <div class="fight-card">
    <!-- Tom Aspinall à gauche -->
    <div class="fighter aspinall">
      <img src="https://ufc.com/images/styles/event_fight_card_upper_body_of_standing_athlete/s3/2025-01/5/ASPINALL_TOM_BELT_L_07-27.png?itok=bFXwFyX_" alt="Tom Aspinall">
      <h2>Tom Aspinall</h2>
      <p>Record : 14-3-0</p>
    </div>

    <!-- COMPARATIF -->
    <div class="stats">
      <h3>Comparatif</h3>
      <table>
        <tr>
          <td>Âge</td>
          <td>30</td>
          <td>33</td>
        </tr>
        <tr>
          <td>Taille</td>
          <td>196 cm</td>
          <td>193 cm</td>
        </tr>
        <tr>
          <td>Poids</td>
          <td>116 kg</td>
          <td>111 kg</td>
        </tr>
        <tr>
          <td>Allonge</td>
          <td>198 cm</td>
          <td>206 cm</td>
        </tr>
      </table>
    </div>

    <!-- Ciryl Gane à droite -->
    <div class="fighter gane">
      <img src="https://ufc.com/images/styles/event_fight_card_upper_body_of_standing_athlete/s3/2025-01/5/GANE_CIRYL_R_03-05.png?itok=4FOqAw9r" alt="Cyril Gane">
      <h2>Ciryl Gane</h2>
      <p>Record : 12-2-0</p>
    </div>
  </div>

  <div class="footer-text">UFC 321 - Heavyweight Bout</div>

</body>
</html>

