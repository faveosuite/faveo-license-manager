<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node;

interface FunctionLike extends Node
{
    /**
     * Whether to return by reference
     *
     * @return bool
     */
    public function returnsByRef() : bool;

    /**
     * List of parameters
     *
     * @return Param[]
     */
    public function getParams() : array;

    /**
     * Get the declared return type or null
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @return null|Identifier|Name|ComplexType
=======
     * @return null|Identifier|Name|NullableType|UnionType
>>>>>>> 22c0e54 (table changes)
=======
     * @return null|Identifier|Name|ComplexType
>>>>>>> f330c64 (optimization in progress)
     */
    public function getReturnType();

    /**
     * The function body
     *
     * @return Stmt[]|null
     */
    public function getStmts();

    /**
     * Get PHP attribute groups.
     *
     * @return AttributeGroup[]
     */
    public function getAttrGroups() : array;
}
