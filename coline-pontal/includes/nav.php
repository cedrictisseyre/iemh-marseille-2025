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

    // descriptions supprimées volontairement
}

?>
