<?php

namespace Illuminate\Foundation\Testing\Concerns;

use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
<<<<<<< HEAD
<<<<<<< HEAD
use Illuminate\Testing\TestComponent;
=======
>>>>>>> 22c0e54 (table changes)
=======
use Illuminate\Testing\TestComponent;
>>>>>>> f330c64 (optimization in progress)
use Illuminate\Testing\TestView;
use Illuminate\View\View;

trait InteractsWithViews
{
    /**
     * Create a new TestView from the given view.
     *
     * @param  string  $view
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @return \Illuminate\Testing\TestView
     */
    protected function view(string $view, array $data = [])
    {
        return new TestView(view($view, $data));
    }

    /**
     * Render the contents of the given Blade template string.
     *
     * @param  string  $template
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @return \Illuminate\Testing\TestView
     */
    protected function blade(string $template, array $data = [])
    {
        $tempDirectory = sys_get_temp_dir();

        if (! in_array($tempDirectory, ViewFacade::getFinder()->getPaths())) {
            ViewFacade::addLocation(sys_get_temp_dir());
        }

        $tempFileInfo = pathinfo(tempnam($tempDirectory, 'laravel-blade'));

        $tempFile = $tempFileInfo['dirname'].'/'.$tempFileInfo['filename'].'.blade.php';

        file_put_contents($tempFile, $template);

        return new TestView(view($tempFileInfo['filename'], $data));
    }

    /**
     * Render the given view component.
     *
     * @param  string  $componentClass
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
<<<<<<< HEAD
<<<<<<< HEAD
     * @return \Illuminate\Testing\TestComponent
=======
     * @return \Illuminate\Testing\TestView
>>>>>>> 22c0e54 (table changes)
=======
     * @return \Illuminate\Testing\TestComponent
>>>>>>> f330c64 (optimization in progress)
     */
    protected function component(string $componentClass, array $data = [])
    {
        $component = $this->app->make($componentClass, $data);

        $view = value($component->resolveView(), $data);

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
        $view = $view instanceof View
            ? $view->with($component->data())
            : view($view, $component->data());

        return new TestComponent($component, $view);
<<<<<<< HEAD
=======
        return $view instanceof View
                ? new TestView($view->with($component->data()))
                : new TestView(view($view, $component->data()));
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    }

    /**
     * Populate the shared view error bag with the given errors.
     *
     * @param  array  $errors
     * @param  string  $key
     * @return $this
     */
    protected function withViewErrors(array $errors, $key = 'default')
    {
        ViewFacade::share('errors', (new ViewErrorBag)->put($key, new MessageBag($errors)));

        return $this;
    }
}
