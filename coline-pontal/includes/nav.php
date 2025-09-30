<?php
function render_nav($active) {
    $tabs = [
        'clubs' => 'Clubs',
        'championnats' => 'Tournois',
        'karateka' => 'Karateka',
        'participation' => 'Participation'
    ];
    echo '<nav class="tabs">';
    foreach ($tabs as $key => $label) {
        $class = ($active === $key) ? 'active' : '';
        echo "<a href='?page=$key' class='$class'>$label</a> ";
    }
    echo '</nav>';

    // descriptions sous les onglets
    echo '<div class="tab-descriptions" style="text-align:center;margin-top:0.6rem;color:var(--muted);">';
    echo '<span class="desc" style="margin-right:1.2rem;">Liste de clubs</span>';
    echo '<span class="desc" style="margin-right:1.2rem;">Liste des tournois</span>';
    echo '<span class="desc" style="margin-right:1.2rem;">Liste des karateka</span>';
    echo '</div>';
}

?>
