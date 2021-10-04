<?php declare(strict_types=1);

namespace PhpParser\Node;

<<<<<<< HEAD
<<<<<<< HEAD
class UnionType extends ComplexType
=======
use PhpParser\NodeAbstract;

class UnionType extends NodeAbstract
>>>>>>> 22c0e54 (table changes)
=======
class UnionType extends ComplexType
>>>>>>> f330c64 (optimization in progress)
{
    /** @var (Identifier|Name)[] Types */
    public $types;

    /**
     * Constructs a union type.
     *
     * @param (Identifier|Name)[] $types      Types
     * @param array               $attributes Additional attributes
     */
    public function __construct(array $types, array $attributes = []) {
        $this->attributes = $attributes;
        $this->types = $types;
    }

    public function getSubNodeNames() : array {
        return ['types'];
    }
    
    public function getType() : string {
        return 'UnionType';
    }
}
