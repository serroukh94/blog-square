<?php declare(strict_types = 1);

namespace Ben\Foundation;

class Config
{
    public static function get(string $config): mixed      // la seule methode qui sera public pour ma classe config ca sera  la methode 'get' pour qui me permet de recuperer une valeur de configuration 
    {
        [$file, $key] = static::getFileAndKey($config);   // pour pouvoir recuperer le fichier et la clef on a utiliser la decomposition [$file ,$key] et du coup pour recuperer ces deux valeurs on a decouper ma chaine de caractere config avec 'getFileAndKey' 
        $path = static::getPath($file);
        $config = require $path;
        return $config[$key] ?? null;
    }

    protected static function getFileAndKey(string $config): array   
    {
        if (!preg_match('/^[a-z_]+\.[a-z_]+$/i', $config)) {    //expression reguliere sur regex101.com.  on a verifier si l'expression reguliere convient a ma chaine de caractere 'config' et pour ca on a utiliser la fonction 'preg_match' 
            throw new \InvalidArgumentException(               // si un argument n'est pas valide c'est le cas de config est invalide
                sprintf('Mauvais format (%s au lieu de fichier.clé (lettres et _ acceptés))', $config)
            );
        }
        return explode('.', $config);
    }

    protected static function getPath(string $file): string
    {
        $path = sprintf('%s/config/%s.php', ROOT, $file);
        if (!file_exists($path)) {
            throw new \InvalidArgumentException('Le fichier de configuration n\'existe pas');
        }
        return $path;
    }
}