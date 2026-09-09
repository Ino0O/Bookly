<?php

namespace App\Enum;

enum BookStatus: string
{
    case A_LIRE = 'À lire';
    case EN_COURS = 'En cours';
    case TERMINE = 'Terminé';
}