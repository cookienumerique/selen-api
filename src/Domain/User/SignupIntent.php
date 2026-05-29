<?php

namespace App\Domain\User;

// Intention déclarée par l'utilisatrice à l'écran d'onboarding "Qu'est-ce qui t'amène ici ?".
// La valeur stockée en base est en anglais ; le libellé affiché à l'utilisatrice est en français (côté app).
enum SignupIntent: string
{
    case RECOMMENDED = 'recommended';            // On m'a parlé de Selen, je viens voir
    case SEEKING_SERENITY = 'seeking_serenity';  // Je cherche un peu de sérénité que je ne trouve pas
    case DAILY_CARE = 'daily_care';              // Je veux prendre soin de moi au quotidien
    case DIFFICULT_TIME = 'difficult_time';      // Je traverse un moment difficile
    case OTHER = 'other';                        // Autre (avec texte libre)
    case SKIPPED = 'skipped';                    // A vu l'écran mais n'a pas répondu
}
