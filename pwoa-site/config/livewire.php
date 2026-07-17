<?php

return [

    /*
    |---------------------------------------------------------------------------
    | Class Namespace
    |---------------------------------------------------------------------------
    |
    | This value sets the root class namespace for Livewire component classes in
    | your application. This value will change where component auto-discovery
    | finds components. It's also referenced by the file creation commands.
    |
    */

    'class_namespace' => 'App\\Livewire',

    /*
    |---------------------------------------------------------------------------
    | View Path
    |---------------------------------------------------------------------------
    |
    | This value is used to specify where Livewire component Blade templates are
    | stored when running file creation commands like `artisan make:livewire`.
    | It is also used if you choose to omit a component's render() method.
    |
    */

    'view_path' => resource_path('views/livewire'),

    /*
    |---------------------------------------------------------------------------
    | Layout
    |---------------------------------------------------------------------------
    | The view that will be used as the layout when rendering a single component
    | as an entire page via `Route::get('/post/create', CreatePost::class);`.
    | In this case, the view returned by CreatePost will render into $slot.
    |
    */

    'layout' => 'components.layouts.app',

    /*
    |---------------------------------------------------------------------------
    | Lazy Loading Placeholder
    |---------------------------------------------------------------------------
    | Livewire allows you to lazy load components that would otherwise slow down
    | the initial page load. Every component can have a custom placeholder or
    | you can define the default placeholder view for all components below.
    |
    */

    'lazy_placeholder' => null,

    /*
    |---------------------------------------------------------------------------
    | Temporary File Uploads
    |---------------------------------------------------------------------------
    |
    | Livewire handles file uploads by first storing them in a temporary directory
    | and then validating them. Here you can configure the disk, validation
    | rules, and directory for those temporary file uploads.
    |
    */

    'temporary_file_upload' => [
        'disk' => null,        // Uses default disk
        'rules' => null,       // Defaults to ['required', 'file', 'max:12288'] (12MB)
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a', 'jpg',
            'jpeg', 'mpga', 'webp', 'pdf',
        ],
        'max_upload_time' => 5, // 5 minutes
    ],

    /*
    |---------------------------------------------------------------------------
    | Request Payload Size Limit
    |---------------------------------------------------------------------------
    |
    | This defines the maximum request payload size allowed in bytes. If a Livewire
    | component's state or file uploads exceed this size, a PayloadTooLargeException
    | is thrown. We increase this to 10MB to accommodate business listing details
    | and document uploads.
    |
    */

    'payload' => [
        'max_size' => 10485760, // 10MB in bytes (10 * 1024 * 1024)
    ],

    /*
    |---------------------------------------------------------------------------
    | Back Button Cache
    |---------------------------------------------------------------------------
    |
    | This setting determines whether Livewire should attempt to cache the state
    | of components when a user navigates away, to improve back-button transitions.
    |
    */

    'back_button_cache' => false,

    /*
    |---------------------------------------------------------------------------
    | Render On Redirect
    |---------------------------------------------------------------------------
    |
    | This determines whether Livewire should render components during a redirect
    | response to speed up page transitions.
    |
    */

    'render_on_redirect' => false,

    /*
    |---------------------------------------------------------------------------
    | Asset Injection
    |---------------------------------------------------------------------------
    |
    | By default, Livewire automatically injects its JavaScript and CSS assets
    | into your Blade templates. If you set this to false, you must manually
    | include `@livewireStyles` and `@livewireScripts` in your layouts.
    |
    */

    'inject_assets' => true,

];
