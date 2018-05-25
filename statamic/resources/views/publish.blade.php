@extends('layout')
@section('content-class', 'publishing')

@section('content')

    <script>
        <?php
            $suggestions = isset($suggestions) ? $suggestions : [];
        ?>
        Statamic.Publish = {
            locale: '{!! $locale !!}',
            contentData: {!! json_encode($content_data) !!},
            suggestions: {!! json_encode($suggestions) !!}
        };
    </script>

    <publish title="{{ $title }}"
             extra="{{ json_encode($extra) }}"
             :is-new="{{ bool_str($is_new) }}"
             content-type="{{ $content_type }}"
             uuid="{{ $uuid }}"
             fieldset-name="{{ $fieldset }}"
             slug="{{ $slug }}"
             uri="{{ $uri }}"
             url="{{ $url }}"
             submit-url="{{ route("{$content_type}.save") }}"
             :status="{{ bool_str($status) }}"
             locale="{{ $locale }}"
             locales="{{ json_encode($locales) }}"
             :is-default-locale="{{ bool_str($is_default_locale) }}"
             :remove-title="true"
    ></publish>

@endsection
