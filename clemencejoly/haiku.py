import random

# Trois listes : une pour chaque ligne du haïku
ligne1 = [
    "Sous la pluie d'été",
    "Un vieux temple perdu",
    "Le vent sur la mer"
]

ligne2 = [
    "des grenouilles bondissent",
    "les pins dansent doucement",
    "la lune éclaire tout"
]

ligne3 = [
    "et le monde s'endort.",
    "le silence revient.",
    "un souffle disparaît."
]

# On choisit une ligne au hasard dans chaque liste
print(random.choice(ligne1))
print(random.choice(ligne2))
print(random.choice(ligne3))
