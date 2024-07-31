<?php

namespace atoum\atoum\tools\parameter;

class analyzer
{
    public function getTypeHintString(\reflectionParameter $parameter, bool $force_nullable = false): string
    {
        if (!$parameter->hasType()) {
            return '';
        }

        $parameterType = $parameter->getType();

        if (
            $parameterType instanceof \reflectionNamedType
            && in_array($parameterType->getName(), ['mixed', 'null'])
        ) {
            // 'mixed' and 'null' cannot be prefixed by nullable flag '?'
            return $parameterType->getName();
        }

        $parameterTypes = $parameterType instanceof \ReflectionUnionType
            ? $parameterType->getTypes()
            : [$parameterType];

        $names = [];
        foreach ($parameterTypes as $type) {
            $name = $type instanceof \reflectionNamedType ? $type->getName() : (string) $type;
            if ($name === 'self') {
                $name = $parameter->getDeclaringClass()->getName();
            }
            $names[] = ($type instanceof \reflectionType && !$type->isBuiltin() ? '\\' : '') . $name;
        }

        if ($parameterType instanceof \ReflectionUnionType && $force_nullable && !in_array('null', $names)) {
            $names[] = 'null';
        }

        $prefix = ($force_nullable || $parameter->allowsNull()) && !($parameterType instanceof \ReflectionUnionType) ? '?' : '';

        return $prefix . implode('|', $names);
    }
}
