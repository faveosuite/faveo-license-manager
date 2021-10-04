<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
<<<<<<< HEAD
<<<<<<< HEAD
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\VariadicPlaceholder;

class New_ extends CallLike
{
    /** @var Node\Name|Expr|Node\Stmt\Class_ Class name */
    public $class;
    /** @var array<Arg|VariadicPlaceholder> Arguments */
=======
=======
use PhpParser\Node\Arg;
>>>>>>> f330c64 (optimization in progress)
use PhpParser\Node\Expr;
use PhpParser\Node\VariadicPlaceholder;

class New_ extends CallLike
{
    /** @var Node\Name|Expr|Node\Stmt\Class_ Class name */
    public $class;
<<<<<<< HEAD
    /** @var Node\Arg[] Arguments */
>>>>>>> 22c0e54 (table changes)
=======
    /** @var array<Arg|VariadicPlaceholder> Arguments */
>>>>>>> f330c64 (optimization in progress)
    public $args;

    /**
     * Constructs a function call node.
     *
     * @param Node\Name|Expr|Node\Stmt\Class_ $class      Class name (or class node for anonymous classes)
<<<<<<< HEAD
<<<<<<< HEAD
     * @param array<Arg|VariadicPlaceholder>  $args       Arguments
=======
     * @param Node\Arg[]                      $args       Arguments
>>>>>>> 22c0e54 (table changes)
=======
     * @param array<Arg|VariadicPlaceholder>  $args       Arguments
>>>>>>> f330c64 (optimization in progress)
     * @param array                           $attributes Additional attributes
     */
    public function __construct($class, array $args = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->class = $class;
        $this->args = $args;
    }

    public function getSubNodeNames() : array {
        return ['class', 'args'];
    }
    
    public function getType() : string {
        return 'Expr_New';
    }
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)

    public function getRawArgs(): array {
        return $this->args;
    }
<<<<<<< HEAD
=======
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
}
