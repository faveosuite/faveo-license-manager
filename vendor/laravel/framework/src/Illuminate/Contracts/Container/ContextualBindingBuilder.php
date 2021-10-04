<?php

namespace Illuminate\Contracts\Container;

interface ContextualBindingBuilder
{
    /**
     * Define the abstract target that depends on the context.
     *
     * @param  string  $abstract
     * @return $this
     */
    public function needs($abstract);

    /**
     * Define the implementation for the contextual binding.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @param  \Closure|string|array  $implementation
=======
     * @param  \Closure|string  $implementation
>>>>>>> 22c0e54 (table changes)
=======
     * @param  \Closure|string|array  $implementation
>>>>>>> f330c64 (optimization in progress)
     * @return void
     */
    public function give($implementation);

    /**
     * Define tagged services to be used as the implementation for the contextual binding.
     *
     * @param  string  $tag
     * @return void
     */
    public function giveTagged($tag);
}
