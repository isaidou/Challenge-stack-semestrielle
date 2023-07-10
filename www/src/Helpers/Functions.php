<?php

declare(strict_types=1);


function ht($value, int $truncateLen = NULL)
{
	$value = htmlspecialchars($value ?? '');
	if (!is_null($truncateLen)) $value = Str::truncateString($value, $truncateLen);

	return $value;
}

/**
 * Récupère une valeur du tableau $data en vérifiant son existence
 * @param array $data Le tableau de données
 * @param string $key La clé à récupérer
 * @param mixed $default La valeur par défaut si la clé n'existe pas dans le tableau
 * @return mixed La valeur correspondante ou la valeur par défaut
 */
function getValueFromArray(array $data, string $key, $default = null)
{
	return isset($data[$key]) ? ht($data[$key]) : $default;
}