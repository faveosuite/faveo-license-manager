<?php

namespace Illuminate\Session;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use SessionHandlerInterface;
use Symfony\Component\Finder\Finder;

class FileSessionHandler implements SessionHandlerInterface
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The path where sessions should be stored.
     *
     * @var string
     */
    protected $path;

    /**
     * The number of minutes the session should be valid.
     *
     * @var int
     */
    protected $minutes;

    /**
     * Create a new file driven handler instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $path
     * @param  int  $minutes
     * @return void
     */
    public function __construct(Filesystem $files, $path, $minutes)
    {
        $this->path = $path;
        $this->files = $files;
        $this->minutes = $minutes;
    }

    /**
     * {@inheritdoc}
<<<<<<< HEAD
<<<<<<< HEAD
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
=======
     */
>>>>>>> 22c0e54 (table changes)
=======
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * {@inheritdoc}
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
<<<<<<< HEAD
=======
     */
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
<<<<<<< HEAD
<<<<<<< HEAD
     *
     * @return string|false
     */
    #[\ReturnTypeWillChange]
=======
     */
>>>>>>> 22c0e54 (table changes)
=======
     *
     * @return string|false
     */
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function read($sessionId)
    {
        if ($this->files->isFile($path = $this->path.'/'.$sessionId)) {
            if ($this->files->lastModified($path) >= Carbon::now()->subMinutes($this->minutes)->getTimestamp()) {
                return $this->files->sharedGet($path);
            }
        }

        return '';
    }

    /**
     * {@inheritdoc}
<<<<<<< HEAD
<<<<<<< HEAD
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
=======
     */
>>>>>>> 22c0e54 (table changes)
=======
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function write($sessionId, $data)
    {
        $this->files->put($this->path.'/'.$sessionId, $data, true);

        return true;
    }

    /**
     * {@inheritdoc}
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
<<<<<<< HEAD
=======
     */
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    public function destroy($sessionId)
    {
        $this->files->delete($this->path.'/'.$sessionId);

        return true;
    }

    /**
     * {@inheritdoc}
<<<<<<< HEAD
<<<<<<< HEAD
     *
     * @return int|false
     */
    #[\ReturnTypeWillChange]
=======
     */
>>>>>>> 22c0e54 (table changes)
=======
     *
     * @return int|false
     */
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function gc($lifetime)
    {
        $files = Finder::create()
                    ->in($this->path)
                    ->files()
                    ->ignoreDotFiles(true)
                    ->date('<= now - '.$lifetime.' seconds');

        foreach ($files as $file) {
            $this->files->delete($file->getRealPath());
        }
    }
}
