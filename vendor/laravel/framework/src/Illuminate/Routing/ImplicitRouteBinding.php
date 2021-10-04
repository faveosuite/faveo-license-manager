<?php

namespace Illuminate\Routing;

use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;

class ImplicitRouteBinding
{
    /**
     * Resolve the implicit route bindings for the given route.
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  \Illuminate\Routing\Route  $route
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function resolveForRoute($container, $route)
    {
        $parameters = $route->parameters();

        foreach ($route->signatureParameters(UrlRoutable::class) as $parameter) {
            if (! $parameterName = static::getParameterName($parameter->getName(), $parameters)) {
                continue;
            }

            $parameterValue = $parameters[$parameterName];

            if ($parameterValue instanceof UrlRoutable) {
                continue;
            }

            $instance = $container->make(Reflector::getParameterClassName($parameter));

            $parent = $route->parentOfParameter($parameterName);

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
            $routeBindingMethod = $route->allowsTrashedBindings()
                        ? 'resolveSoftDeletableRouteBinding'
                        : 'resolveRouteBinding';

<<<<<<< HEAD
            if ($parent instanceof UrlRoutable && in_array($parameterName, array_keys($route->bindingFields()))) {
                $childRouteBindingMethod = $route->allowsTrashedBindings()
                            ? 'resolveSoftDeletableChildRouteBinding'
                            : 'resolveChildRouteBinding';

                if (! $model = $parent->{$childRouteBindingMethod}(
=======
            if ($parent instanceof UrlRoutable && in_array($parameterName, array_keys($route->bindingFields()))) {
                if (! $model = $parent->resolveChildRouteBinding(
>>>>>>> 22c0e54 (table changes)
=======
            if ($parent instanceof UrlRoutable && in_array($parameterName, array_keys($route->bindingFields()))) {
                $childRouteBindingMethod = $route->allowsTrashedBindings()
                            ? 'resolveSoftDeletableChildRouteBinding'
                            : 'resolveChildRouteBinding';

                if (! $model = $parent->{$childRouteBindingMethod}(
>>>>>>> f330c64 (optimization in progress)
                    $parameterName, $parameterValue, $route->bindingFieldFor($parameterName)
                )) {
                    throw (new ModelNotFoundException)->setModel(get_class($instance), [$parameterValue]);
                }
<<<<<<< HEAD
<<<<<<< HEAD
            } elseif (! $model = $instance->{$routeBindingMethod}($parameterValue, $route->bindingFieldFor($parameterName))) {
=======
            } elseif (! $model = $instance->resolveRouteBinding($parameterValue, $route->bindingFieldFor($parameterName))) {
>>>>>>> 22c0e54 (table changes)
=======
            } elseif (! $model = $instance->{$routeBindingMethod}($parameterValue, $route->bindingFieldFor($parameterName))) {
>>>>>>> f330c64 (optimization in progress)
                throw (new ModelNotFoundException)->setModel(get_class($instance), [$parameterValue]);
            }

            $route->setParameter($parameterName, $model);
        }
    }

    /**
     * Return the parameter name if it exists in the given parameters.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return string|null
     */
    protected static function getParameterName($name, $parameters)
    {
        if (array_key_exists($name, $parameters)) {
            return $name;
        }

        if (array_key_exists($snakedName = Str::snake($name), $parameters)) {
            return $snakedName;
        }
    }
}
