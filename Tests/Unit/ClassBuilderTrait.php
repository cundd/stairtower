<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit;

trait ClassBuilderTrait
{
    /**
     * Build the given class
     *
     * @param string $className
     * @param string $extends
     * @return bool
     */
    public static function buildClass(string $className, string $extends = ''): bool
    {
        if (class_exists($className)) {
            return false;
        }

        static::buildClassOrInterface('class', $className, $extends);

        return class_exists($className, false);
    }

    /**
     * @param string $type
     * @param string $className
     * @param string $extends
     */
    private static function buildClassOrInterface(string $type, string $className, string $extends)
    {
        $lastSlashPos = strrpos($className, '\\');
        if ($lastSlashPos !== false) {
            $namespace = ltrim(substr($className, 0, $lastSlashPos), '\\');
            $preparedClassName = substr($className, $lastSlashPos + 1);
        } else {
            $namespace = '';
            $preparedClassName = $className;
        }


        if ($namespace) {
            $lines[] = "namespace $namespace;";
        }
        $lines[] = "$type $preparedClassName";
        if ($extends) {
            if ($extends[0] !== '\\') {
                $extends = '\\' . $extends;
            }
            $lines[] = "extends $extends";
        }
        $lines[] = '{}';

        $code = implode(' ', $lines);

        eval($code);
    }
}
