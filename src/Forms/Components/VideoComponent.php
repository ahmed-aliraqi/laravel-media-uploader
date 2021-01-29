<?php

namespace AhmedAliraqi\LaravelMediaUploader\Forms\Components;

use Laraeast\LaravelBootstrapForms\Components\BaseComponent;

class VideoComponent extends BaseComponent
{
    /**
     * The component view path.
     *
     * @var string
     */
    protected $viewPath = 'uploader::video';

    /**
     * @var \Illuminate\Contracts\Support\Arrayable
     */
    protected $files;

    /**
     * @var int
     */
    protected $max;

    /**
     * @var string
     */
    protected $notes;

    /**
     * @var string
     */
    protected $form;

    /**
     * @var bool
     */
    protected $unlimited = false;

    /**
     * @var string
     */
    protected $collection = 'default';

    /**
     * Initialized the input arguments.
     *
     * @param mixed ...$arguments
     * @return $this
     */
    public function init(...$arguments)
    {
        $this->name = $name = $arguments[0] ?? null;

        $this->value = $arguments[1] ?? null;

        $this->setDefaultLabel();

        $this->setDefaultNote();

        return $this;
    }

    /**
     * Set the stored files.
     *
     * @param array $files
     * @return $this
     */
    public function files($files = [])
    {
        $this->files = $files;

        return $this;
    }

    /**
     * Upload unlimited files.
     *
     * @return $this
     */
    public function unlimited()
    {
        $this->unlimited = true;

        return $this;
    }

    /**
     * Set the media's form.
     *
     * @return $this
     */
    public function form($form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Set the maximum files length.
     *
     * @param int $max
     * @return $this
     */
    public function max($max = 1)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Set the maximum files length.
     *
     * @param null $collection
     * @return $this
     */
    public function collection($collection = null)
    {
        $this->collection = $collection ?: 'default';

        return $this;
    }

    /**
     * @param string $notes
     * @return $this
     */
    public function notes($notes = null)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * The variables with registered in view component.
     *
     * @return array
     */
    protected function viewComposer()
    {
        return [
            'files' => $this->files,
            'max' => $this->max,
            'notes' => $this->notes,
            'collection' => $this->collection,
            'form' => $this->form,
            'unlimited' => $this->unlimited,
        ];
    }
}
