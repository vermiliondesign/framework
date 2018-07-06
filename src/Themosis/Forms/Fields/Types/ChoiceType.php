<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Contracts\CheckableInterface;
use Themosis\Forms\Contracts\SelectableInterface;
use Themosis\Forms\Fields\ChoiceList\ChoiceList;
use Themosis\Forms\Transformers\ChoiceToValueTransformer;

class ChoiceType extends BaseType implements CheckableInterface, SelectableInterface
{
    /**
     * Field layout.
     *
     * @var string
     */
    protected $layout = 'select';

    /**
     * ChoiceType field view.
     *
     * @var string
     */
    protected $view = 'types.choice';

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->allowedOptions = $this->setAllowedOptions();
        $this->defaultOptions = $this->setDefaultOptions();
    }

    /**
     * Define the field allowed options.
     *
     * @return array
     */
    protected function setAllowedOptions()
    {
        return array_merge($this->allowedOptions, [
            'choices',
            'expanded',
            'multiple'
        ]);
    }

    /**
     * Define the field default options values.
     *
     * @return array
     */
    protected function setDefaultOptions()
    {
        return array_merge($this->defaultOptions, [
            'expanded' => false,
            'multiple' => false,
            'choices' => null
        ]);
    }

    /**
     * Parse and setup some default options if not set.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options): array
    {
        $this->setTransformer(new ChoiceToValueTransformer());

        $options = parent::parseOptions($options);

        if (is_null($options['choices'])) {
            $options['choices'] = [];
        }

        if (is_array($options['choices'])) {
            $options['choices'] = new ChoiceList($options['choices']);
        }

        // Set field layout based on field options.
        $this->setLayout($options['expanded'], $options['multiple']);

        // Set the "multiple" attribute for <select> tag.
        if ('select' === $this->getLayout() && $options['multiple']) {
            // We're using a <select> tag with the multiple option set to true.
            // So we're going to directly inject the multiple attribute.
            $options['attributes'][] = 'multiple';
        }

        return $options;
    }

    /**
     * Set the field layout option.
     *
     * @param bool $expanded
     * @param bool $multiple
     *
     * @return $this
     */
    protected function setLayout($expanded = false, $multiple = false)
    {
        if ($expanded && false === $multiple) {
            $this->layout = 'radio';
        } elseif ($expanded && $multiple) {
            $this->layout = 'checkbox';
        }

        return $this;
    }

    /**
     * Retrieve the field layout.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @inheritdoc
     *
     * @param callable $callback
     * @param array    $args
     *
     * @return string
     */
    public function checked(callable $callback, array $args): string
    {
        return call_user_func_array($callback, $args);
    }

    /**
     * @inheritdoc
     *
     * @param callable $callback
     * @param array    $args
     *
     * @return string
     */
    public function selected(callable $callback, array $args): string
    {
        return call_user_func_array($callback, $args);
    }
}
