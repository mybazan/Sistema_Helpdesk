<?php

namespace App\Services;

/**
 * Servicio de validación de datos.
 * 
 * Esta clase proporciona métodos para validar distintos tipos de datos,
 * asegurando que cumplan con las reglas de formato establecidas.
 */
class Validator
{

    /**
     * Valida que el nombre contenga solo letras y espacios.
     * No se permiten números, símbolos ni caracteres especiales.
     *
     * @param string $nombre El nombre a validar.
     * @return bool Devuelve `true` si el nombre es válido, `false` en caso contrario.
     */
    public function validaNombre(string $nombre): bool
    {
        return preg_match('/^[\p{L}\s]+$/u', $nombre) === 1; 
    }
}