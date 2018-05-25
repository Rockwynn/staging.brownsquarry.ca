<?php

namespace Statamic\CP;

use Statamic\API\Str;
use Statamic\API\File;
use Statamic\API\Path;
use Statamic\API\Taxonomy;
use Statamic\API\YAML;
use Statamic\API\Fieldset as FieldsetAPI;
use Statamic\Contracts\CP\Fieldset as FieldsetContract;

/**
 * A fieldset
 */
class Fieldset implements FieldsetContract
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type = 'default';

    /**
     * @var string
     */
    private $locale;

    /**
     * @var array
     */
    private $contents;

    /**
     * @var array
     */
    private $fieldtypes;

    /**
     * Initialize a new fieldset
     */
    public function __construct()
    {
        $this->locale(default_locale());
    }

    /**
     * Get or set the type
     *
     * @param string|null $type
     * @return mixed
     * @throws \Exception
     */
    public function type($type = null)
    {
        if (is_null($type)) {
            return $this->type;
        }

        if (! in_array($type, ['default', 'global', 'settings', 'fieldtype', 'theme', 'addon'])) {
            throw new \Exception('Invalid fieldset type. Must be `default`, `global`, `settings`, `fieldtype`, `theme`, or `addon`.');
        }

        $this->type = $type;
    }

    /**
     * Get or set the locale
     *
     * @param string|null $locale
     * @return mixed
     */
    public function locale($locale = null)
    {
        if (is_null($locale)) {
            return $this->locale;
        }

        $this->locale = $locale;
    }

    /**
     * Get or set the name
     *
     * @param string|null $name
     * @return mixed
     */
    public function name($name = null)
    {
        if (is_null($name)) {
            return $this->name;
        }

        $this->name = $name;
    }

    /**
     * Get or set the title
     *
     * Either the `title` key, or fallback to the filename
     *
     * @param string|null $title
     * @return mixed
     */
    public function title($title = null)
    {
        if (! is_null($title)) {
            $this->contents['title'] = $title;
        }

        if (! $title = array_get($this->contents(), 'title')) {
            $title = ucfirst($this->name());
        }

        return $title;
    }

    /**
     * Get or set whether this fieldset is hidden from the selection dialog
     *
     * @param  bool|null $hidden
     * @return bool
     */
    public function hidden($hidden = null)
    {
        if (is_null($hidden)) {
            return array_get($this->contents, 'hide', false);
        }

        $this->contents['hide'] = $hidden;
    }

    /**
     * Get the path to the file
     *
     * @return string
     */
    public function path()
    {
        if ($this->type === 'addon') {
            list($addon, $name) = explode('.', $this->name());
            return Path::makeRelative(addons_path($addon.'/'.$name . '.yaml'));
        } elseif ($this->type === 'settings') {
            $path = statamic_path('settings/fieldsets/');
        } else {
            $path = settings_path('fieldsets/');
        }

        return Path::makeRelative($path . $this->name() . '.yaml');
    }

    /**
     * Get or set the contents
     *
     * @param array|null $contents
     * @return mixed
     */
    public function contents($contents = null)
    {
        if (is_null($contents)) {
            return $this->contents;
        }

        $this->contents = $contents;
    }

    public function sections()
    {
        $contents = $this->contents();

        // Grab the sections. If there are none defined, we're going to put
        // everything into one main section so we'll define that here.
        $sections = array_get($contents, 'sections', [
            'main' => ['fields' => array_get($contents, 'fields', [])]
        ]);

        // Put the fields at the end. It just makes things easier to read.
        $sections = collect($sections)->map(function ($section) {
            $section['fields'] = array_pull($section, 'fields', []);
            return $section;
        })->all();

        return $sections;
    }

    /**
     * Get or set the fields
     *
     * @param array|null $fields
     * @param boolean $inline_partials
     * @return mixed
     */
    public function fields($fields = null, $inline_partials = true)
    {
        if (! is_null($fields)) {
            // @todo
            throw new \Exception('Cannot set fields.');
            $this->contents['fields'] = $fields;
        }

        $fields = collect($this->sections())
            ->flatMap(function ($section) {
                return $section['fields'];
            })->all();

        if ($inline_partials) {
            $fields = $this->inlinePartials($fields);
        }

        return $fields;
    }

    /**
     * Get all fields, without inlining partials
     *
     * @return array
     */
    public function fieldsWithPartials()
    {
        return $this->fields(null, false);
    }

    /**
     * Bring the fields in partial fieldsets into the parent
     *
     * @param  array $fields
     * @return array
     */
    private function inlinePartials($fields)
    {
        return collect($fields)->mapWithKeys(function ($item, $key) {

            if (array_get($item, 'type', 'text') === 'partial') {
                return $this->fieldsToArray(FieldsetAPI::get($item['fieldset'])->fields(), true);

            } elseif ($sets = array_get($item, 'sets')) {

                foreach ($sets as $set => $config) {
                    $sets[$set]['fields'] = $this->inlinePartials(array_get($config, 'fields'));
                }

                $item['sets'] = $sets;

            } elseif ($other_fields = array_get($item, 'fields')) {
                $item['fields'] = $this->inlinePartials($other_fields);
            }

            return [$key => $item];
        })->all();
    }

    /**
     * Get the fieldtypes in the fieldset
     *
     * @return \Statamic\Extend\Fieldtype[]
     */
    public function fieldtypes()
    {
        if ($this->fieldtypes) {
            return $this->fieldtypes;
        }

        $fieldtypes = [];

        $configs = array_merge(
            $this->getTaxonomyFieldConfigs(),
            $this->fields()
        );

        foreach ($configs as $name => $config) {
            // When merging fields without configs, they'd just be
            // empty strings so we'll set them to empty arrays.
            if (! is_array($config)) {
                $config = [];
            }

            $type = array_get($config, 'type', 'text');
            $config['name'] = $name;
            $fieldtypes[] = FieldtypeFactory::create($type, $config);
        }

        return $this->fieldtypes = $fieldtypes;
    }

    /**
     * Get the taxonomy field configurations
     *
     * @return array
     */
    private function getTaxonomyFieldConfigs()
    {
        if ($this->type() !== 'default') {
            return [];
        }

        // If the configuration has been specifically set to false, there should be none.
        if (! $this->taxonomies()) {
            return [];
        }

        return collect(Taxonomy::handles())->map(function ($handle) {
            return [
                'type' => 'taxonomy',
                'name' => $handle,
            ];
        })->keyBy('name')->all();
    }

    /**
     * Save the fieldset to file
     */
    public function save()
    {
        $contents = $this->contents;

        // Remove falsey keys where the defaults are falsey
        foreach (['hide', 'author', 'template'] as $falsey) {
            if (array_get($contents, $falsey) === false) {
                unset($contents[$falsey]);
            }
        }

        if (empty($contents['date'])) {
            unset($contents['date']);
        }

        $sections = $this->sections();

        // If there's only one section, and it's the one that is automatically generated,
        // we'll just save the fields array and prevent saving a sections array at all.
        if ($this->shouldOnlySaveFields()) {
            unset($contents['sections']);
            $contents['fields'] = $sections['main']['fields'];
        } else {
            unset($contents['fields']);
            $contents['sections'] = $sections;
        }

        $yaml = YAML::dump($contents);

        File::put($this->path(), $yaml);
    }

    private function shouldOnlySaveFields()
    {
        $sections = $this->sections();

        return !isset($this->contents['sections'])
            && count($sections) === 1
            && isset($sections['main'])
            && !isset($sections['main']['display']);
    }

    public static function cleanFieldForSaving($field)
    {
        // When a JS submission is involved, the fields array contains
        // some extra data that is used for display purposes.
        // We don't need them in the actual fieldset, so we'll get rid of those.
        $field = array_except($field, ['id', 'name', 'isNew', 'isMeta']);

        // Fields are 100% by default. Don't need to save it.
        if (in_array(array_get($field, 'width'), [100, '100'])) {
            unset($field['width']);
        }

        // Remove required field
        // We add it to the toArray so it can be used elsewhere,
        // but we don't want it added to the file itself.
        unset($field['required']);

        // Blank keys can be discarded.
        $field = self::discardBlankKeys($field);

        return $field;
    }

    /**
     * Discard any blank/falsey keys
     *
     * @param  array $array
     * @return array
     */
    private static function discardBlankKeys($array)
    {
        foreach ($array as $key => $value) {
            // We want to keep falsey values in these keys.
            if (in_array($key, ['show_when', 'hide_when'])) {
                continue;
            }

            // Get rid of literal false values in these keys
            if (in_array($key, ['localizable'])) {
                if ($value === false) {
                    unset($array[$key]);
                }
            }

            if (is_array($value)) {
                // Recursion!
                $array[$key] = self::discardBlankKeys($value);
            } else {
                // Strip out nulls and empty strings. We want to keep literal false values.
                if (in_array($value, [null, ''], true)) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }

    public function delete()
    {
        File::delete($this->path());
    }

    /**
     * Get the instance as an array.
     *
     * @param boolean $inlinePartials
     * @return array
     */
    public function toArray($inlinePartials = true)
    {
        $contents = $this->contents();

        $contents['sections'] = $this->sectionsToArray($inlinePartials);
        $contents['taxonomies'] = $this->taxonomies();

        return array_except($contents, ['fields']);
    }

    private function sectionsToArray($inlinePartials)
    {
        return collect($this->sections())->map(function ($section, $handle) use ($inlinePartials) {
            return [
                'display' => array_get($section, 'display', ucfirst($handle)),
                'handle' => $handle,
                'fields' => array_values($this->fieldsToArray($section['fields'], $inlinePartials)),
            ];
        })->values()->all();
    }

    private function fieldsToArray($fields, $inlinePartials)
    {
        $fields = collect($fields)->map(function ($config, $name) {
            // When merging fields without configs, they'd just be
            // empty strings so we'll set them to empty arrays.
            if (! is_array($config)) {
                $config = [];
            }

            // Useful to have the field name as part of the array itself
            $config['name'] = $name;

            // Populate with appropriate defaults and fallbacks
            $config['type'] = array_get($config, 'type', 'text');
            $config['display'] = $this->getDisplayText($name, $config);
            $config['instructions'] = $this->getInstructionsText($name, $config);
            $config['required'] = Str::contains(array_get($config, 'validate'), 'required');
            $config['localizable'] = $name === 'title' ? true : array_get($config, 'localizable', false);

            return $this->preProcess($config);
        });

        if ($inlinePartials) {
            $fields = $this->inlinePartials($fields);
        }

        return collect($fields)->all();
    }

    /**
     * Pre-process config fields
     *
     * Takes a field's config, and pre-processes each field by it's appropriate
     * fieldtype's preProcess method.
     *
     * For example, a replicator field's `sets` key is actually a ReplicatorSets fieldtype.
     * It will modify the array by moving the keys (set names) into each array.
     *
     * @param array $config
     * @return array
     * @throws \Statamic\Exceptions\FatalException
     */
    private function preProcess($config)
    {
        $fieldtype = FieldtypeFactory::create($config['type']);
        $fieldtypes = $fieldtype->getConfigFieldset()->fieldtypes();

        // Go through each fieldtype in the config fieldset and process the values.
        foreach ($fieldtypes as $field) {
            if (! in_array($field->getName(), array_keys($config))) {
                continue;
            }

            $field->is_config = true;
            $config[$field->getName()] = $field->preProcess($config[$field->getName()]);
        }

        return $config;
    }

    private function getDisplayText($name, $config)
    {
        if ($display = array_get($config, 'display')) {
            return $display;
        }

        if ($this->type === 'settings') {
            $key = "fieldsets/{$this->name}.{$name}";
            $translation = translate($key);
            if ($translation !== $key) {
                return $translation;
            }

            $key = 'fields.'.$name;
            $translation = translate($key);
            if ($translation !== $key) {
                return $translation;
            }
        }

        if ($this->type === 'addon') {
            $addon = explode('.', $this->name())[0];
            $key = "addons.{$addon}::settings.{$name}";
            $translation = translate($key);
            if ($translation !== $key) {
                return $translation;
            }
        }

        return null;
    }

    private function getInstructionsText($name, $config)
    {
        if ($instructions = array_get($config, 'instructions')) {
            return $instructions;
        }

        if ($this->type === 'settings') {
            $key = "fieldsets/{$this->name}.{$name}_instruct";
            $translation = translate($key);
            if ($translation !== $key) {
                return $translation;
            }

            $key = 'fields.'.$name.'_instruct';
            $translation = translate($key);
            if ($translation !== $key) {
                return $translation;
            }
        }

        if ($this->type === 'addon') {
            $addon = explode('.', $this->name())[0];
            $key = "addons.{$addon}::settings.{$name}_instruct";
            $translation = translate($key);
            if ($translation !== $key) {
                return $translation;
            }
        }

        return null;
    }

    /**
     * The URL to edit it in the CP
     *
     * @return mixed
     */
    public function editUrl()
    {
        return cp_route('fieldset.edit', $this->name());
    }

    /**
     * Get or set whether to append taxonomies
     *
     * @param bool|null $taxonomies
     * @return bool
     */
    public function taxonomies($taxonomies = null)
    {
        if (is_null($taxonomies)) {
            return array_get($this->contents, 'taxonomies', true);
        }

        $this->contents['taxonomies'] = $taxonomies;
    }
}
