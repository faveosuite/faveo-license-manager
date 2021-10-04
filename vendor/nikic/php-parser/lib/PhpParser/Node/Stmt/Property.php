<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
<<<<<<< HEAD
<<<<<<< HEAD
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
=======
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
>>>>>>> 22c0e54 (table changes)
=======
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
>>>>>>> f330c64 (optimization in progress)

class Property extends Node\Stmt
{
    /** @var int Modifiers */
    public $flags;
    /** @var PropertyProperty[] Properties */
    public $props;
<<<<<<< HEAD
<<<<<<< HEAD
    /** @var null|Identifier|Name|ComplexType Type declaration */
=======
    /** @var null|Identifier|Name|NullableType|UnionType Type declaration */
>>>>>>> 22c0e54 (table changes)
=======
    /** @var null|Identifier|Name|ComplexType Type declaration */
>>>>>>> f330c64 (optimization in progress)
    public $type;
    /** @var Node\AttributeGroup[] PHP attribute groups */
    public $attrGroups;

    /**
     * Constructs a class property list node.
     *
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
     * @param int                                     $flags      Modifiers
     * @param PropertyProperty[]                      $props      Properties
     * @param array                                   $attributes Additional attributes
     * @param null|string|Identifier|Name|ComplexType $type       Type declaration
     * @param Node\AttributeGroup[]                   $attrGroups PHP attribute groups
<<<<<<< HEAD
=======
     * @param int                                                $flags      Modifiers
     * @param PropertyProperty[]                                 $props      Properties
     * @param array                                              $attributes Additional attributes
     * @param null|string|Identifier|Name|NullableType|UnionType $type       Type declaration
     * @param Node\AttributeGroup[]                              $attrGroups PHP attribute groups
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
     */
    public function __construct(int $flags, array $props, array $attributes = [], $type = null, array $attrGroups = []) {
        $this->attributes = $attributes;
        $this->flags = $flags;
        $this->props = $props;
        $this->type = \is_string($type) ? new Identifier($type) : $type;
        $this->attrGroups = $attrGroups;
    }

    public function getSubNodeNames() : array {
        return ['attrGroups', 'flags', 'type', 'props'];
    }

    /**
     * Whether the property is explicitly or implicitly public.
     *
     * @return bool
     */
    public function isPublic() : bool {
        return ($this->flags & Class_::MODIFIER_PUBLIC) !== 0
            || ($this->flags & Class_::VISIBILITY_MODIFIER_MASK) === 0;
    }

    /**
     * Whether the property is protected.
     *
     * @return bool
     */
    public function isProtected() : bool {
        return (bool) ($this->flags & Class_::MODIFIER_PROTECTED);
    }

    /**
     * Whether the property is private.
     *
     * @return bool
     */
    public function isPrivate() : bool {
        return (bool) ($this->flags & Class_::MODIFIER_PRIVATE);
    }

    /**
     * Whether the property is static.
     *
     * @return bool
     */
    public function isStatic() : bool {
        return (bool) ($this->flags & Class_::MODIFIER_STATIC);
    }

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
    /**
     * Whether the property is readonly.
     *
     * @return bool
     */
    public function isReadonly() : bool {
        return (bool) ($this->flags & Class_::MODIFIER_READONLY);
    }

<<<<<<< HEAD
=======
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    public function getType() : string {
        return 'Stmt_Property';
    }
}
