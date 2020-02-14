# Framework

A WordPress theme development framework that comes built with various helper methods as well as a templating system so you wouldn't have to scream at the awful mix of PHP & HTML that has haunted WordPress for ages.

- [Install](#install)
- [Using Framework in PHP](#using-framework-in-php)
  - [First of all](#first-of-all)
  - [Helper methods](#helper-methods)
    - [Initializing theme](#initializing-theme)
    - [Creating a navigation menu](#creating-a-navigation-menu)
    - [Adding scripts and styles](#adding-scripts-and-styles)
    - [Sending an e-mail](#sending-an-email)
    - [Creating AJAX actions](#creating-ajax-actions)
    - [Getting the current object](#getting-the-current-object)
    - [Retrieving user input](#retrieving-user-input)
    - [Getting the current pagination number](#getting-the-current-pagination-number)
    - [Registering custom post types](#registering-custom-post-types)
    - [Registering custom taxonomies](#registering-custom-taxonomies)
  - [Conditional callbacks](#conditional-callbacks)
    - [isFrontPage](#isFrontPage)
    - [isHome](#isHome)
    - [isPage](#isPage)
    - [isSingle](#isSingle)
    - [isCategory](#isCategory)
    - [isTag](#isTag)
    - [isPostType](#isPostType)
    - [isPostTypeArchive](#isPostTypeArchive)
    - [isArchive](#isArchive)
    - [isAuthor](#isAuthor)
    - [isTax](#isTax)
    - [isSearch](#isSearch)
    - [is404](#is404)
    - [isUserLoggedIn](#isUserLoggedIn)
  - [Templating](#templating)
    - [Getting started](#getting-started)
    - [Partials](#partials)
    - [Helpers](#helpers)
    - [Variables](#variables)
    - [Querying content](#querying-content)
  - [Extending](#extending)
- [Using Framework in JavaScript](#using-framework-in-javascript)
  - [Creating AJAX actions](#creating-ajax-actions)
- [Changelog](#changelog)

## Install

1. Download the latest version from [releases](https://github.com/digital-baboon/framework/releases).
2. Upload the `.zip` file in your `WordPress Admin > Plugins > Add New > Upload` page.
3. All good. You can now access all of the Framework PHP and JS methods.

**Note:** This plugin works with WordPress 5.3.2 and up.

Oh and our own site is built with this, so if you want to take a look at how we use this framework, check out [the WordPress theme](https://github.com/digital-baboon/baboontheme) we've made with it. 
Additionally there is also the [Boilerplate](https://github.com/digital-baboon/boilerplate) if you wish to kickstart your theme development using Framework.

## Using Framework in PHP

Writing app logic happens in the `functions.php` file of the theme.

### First of all

Before you can begin to use Framework, initialize it first, like so:

```php
$app = new Framework();
```

### Helper methods

#### Initializing theme

You can initialize the theme with sensible defaults by calling the `init` method, like so:

```php
$app->init();
```

This will load the sensible defaults as:

- `title-tag` - Enabling WordPress itself to deal with the page titles
- `post-thumbnails` - Adds post thumbnails support
- `html5` - Enables HTML5
- `automatic-feed-links` - Adds automatic feed links
- `framework-js` - Loads the Framework JS library

If you wish to exclude things from initializing, simply pass an array with keys of items to exclude, for example to exclude the `title-tag`, simply run the `init` like this:

```php
$app->init(['title-tag']);
```

#### Creating a navigation menu

To create a navigation menu, simply call the `menu` method. An example is:

```php
$app->menu('primary-menu', 'Primary Menu');
```

Where the first argument takes the ID of the menu and the second argument the name of the menu.

#### Adding scripts and styles

To enqueue scripts and styles in your theme, simply use the `script` and `style` methods. For example, to add a script to the `<head>` of your theme, simply do this:

```php
$app->script('script-id', 'uri-to-script.js');
```

Likewise with the style:

```php
$app->style('style-id', 'uri-to-style.css');
```

#### Sending an e-mail

You can send an email via the `sendMessage` method, a fully configured example is:

```php
$app->sendMessage([
  'to' => 'asko@digitalbaboon.com',
  'subject' => 'Contact Form | Digital Baboon',
  'body' => '<ul><li>Name: {name}</li><li>E-mail: {email}</li><li>Company: {companyName}</li></ul><p>{message}</p>',
  'data' => [
    'name' => 'John Smith',
    'email' => 'john@smith.com',
    'companyName' => 'John Smith & Partners',
    'message' => 'Hi there!'
  ]
]);
```

As you can see, anything within curly brackets is the key of an item passed in `data`. For example, if your `body` has a string like `'Hi {name}!'`, then to replace the `{name}` part with an actual thing, simply pass `'name' => 'John Smith'` to `data`. The rest of the configuration should be pretty self-explanatory.

#### Creating AJAX actions

You can create ajax actions with the `ajaxAction` method, a fully configured example is:

```php
$app->ajaxAction('send_message', function() {

  // Do whatever you want here.
  // For example: call $app->sendMessage()

});
```

This would create the actions for both private and public ajax actions. 

#### Getting the current object

Getting the currently in scope object can be achieved with the `current` method, for example if you're on a tag page where the tag name is "Photos" then to get URL slug that represents it, you can do something like this:

```php
$slug = $app->current()->slug; // returns "photos"
```

Likewise if you are on a post page and want to get the post ID, you can do this:

```php
$id = $app->current()->ID;
```

If you want to do know percicely what it returns, just `var_dump` it in the context you need.

#### Retrieving user input

To retrieve user input, you can use the `input` method, like so:

```php
$name = $app->input('name');
```

This retrieves both $_GET and $_POST inputs as well as sanitizes them.

#### Getting the current pagination number

To get the current pagination number, which is useful in a `query`, for example, call `paged`, like so:

```php
$paged = $app->paged();
```

#### Registering custom post types

To register a custom post type, call `registePostType`, like so:

```php
$app->registerPostType('book', 'post', 'Book', 'Books', [
  'title',
  'editor',
  'author',
  'thumbnail',
  'excerpt',
  'trackbacks',
  'custom-fields',
  'comments',
  'revisions'
]);
```

1. The first argument is the ID of the post type, the name by which you will programmatically call it. 
2. The second argument is the capability or type of the post type. It can be either `post` or `page`.
3. The third argument is the singular name of the post type for use in the WordPress admin.
4. The fourth argument is the plural name of the post type for use in the WordPress admin.
5. The fifth argument is an array of features that the custom post type will support.

#### Registering custom taxonomies

To register a custom taxonomy, call `registerTaxonomy`, like so:

```php
$app->registerTaxonomy('book_categories', 'book', [

]);
```

The first argument is the taxonomy name in slug form, the second is the post type it should tie itself to and the third is an 
array of [arguments that you can pass to the taxonomy](https://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments).

### Conditional callbacks

In classic WordPress theme development you can use conditionals such as `isFrontPage` for the front page and `isPage` for the page to do logical work, you can do the same, but with callback functions. This is especially useful if used with Framework's templating, but you can use for whatever use case you may find as well.

#### isFrontPage

To call a callback function when `is_front_page` conditional is `true`, do the following:

```php
$app->isFrontPage(function() use($app) {

  // your code goes here.

});
```

#### isHome

To call a callback function when `is_home` conditional is `true`, do the following:

```php
$app->isHome(function() use($app) {

  // your code goes here.
  
});
```

#### isPage

To call a callback function when `is_page` conditional is `true`, do the following:

```php
$app->isPage('*', function() use($app) {

  // your code goes here.
  
});
```

Likewise replace `*` with the page slug, ID or array of them to fire the callback only on a specific page/pages.

#### isSingle

To call a callback function when `is_single` conditional is `true`, do the following:

```php
$app->isSingle('*', function() use($app) {

  // your code goes here.
  
});
```

Likewise replace `*` with the post slug, ID or array of them to fire the callback only on a specific post/posts.

#### isCategory

To call a callback function when `is_category` conditional is `true`, do the following:

```php
$app->isCategory('*', function() use($app) {

  // your code goes here.
  
});
```

Replace `*` with the category slug, ID or array of them to fire the callback only on a specific category/categories.

#### isTag

To call a callback function when `is_tag` conditional is `true`, do the following:

```php
$app->isTag('*', function() use($app) {

  // your code goes here.
  
});
```

Replace `*` with the tag slug, ID or array of them to fire the callback only on a specific tag/tags.

#### isPostType

To call a callback function when `is_post_type` conditional is `true`, do the following:

```php
$app->isPostType('post-type-slug', function() use($app) {

  // your code goes here.
  
});
```

Replace `is-post-type-slug` with the post type name in slug form.

#### isPostTypeArchive

To call a callback function when `is_post_type_slug` conditional is `true`, do the following:

```php
$app->isPostTypeArchive('post-type-slug', function() use($app) {

  // your code goes here.
  
});
```

Replace `is-post-type-slug` with the post type name in slug form.

#### isArchive

To call a callback function when `is_archive` conditional is `true`, do the following:

```php
$app->isArchive(function() use($app) {

  // your code goes here.
  
});
```

#### isAuthor

To call a callback function when `is_author` conditional is `true`, do the following:

```php
$app->isAuthor('*', function() use($app) {

  // your code goes here.
  
});
```

Likewise replace `*` with the author slug, ID or array of them to fire the callback only on a specific author/authors.

#### isTax

To call a callback function when `is_tax` conditional is `true`, do the following:

```php
$app->isTax('*', '*', function() use($app) {

  // your code goes here.
  
});
```

The first argument takes the taxonomy name, or asterisk (*) when matching any taxonomy, and the second argument
takes the slug, ID or array of either of the terms in the taxonomy. 

#### isSearch

To call a callback function when `is_search` conditional is `true`, do the following:

```php
$app->isSearch(function() use($app) {

  // your code goes here.
  
});
```

#### is404

To call a callback function when `is_404` conditional is `true`, do the following:

```php
$app->is404(function() use($app) {

  // your code goes here.
  
});
```

#### isUserLoggedIn

To call a callback function when `is_user_logged_in` conditional is `true`, do the following:

```php
$app->isUserLoggedIn(function() use($app) {

  // your code goes here.
  
});
```

### Templating

If you wish to abandon the mess of mixing PHP with HTML, you can do so with Framework's templating system. To use the templating system, you need to first remove all PHP files from your theme directory (except `functions.php` and `index.php`, because we do our logic in `functions.php` and WordPress requires a `index.php`, but the `index.php` just leave empty). 

Once you've done that, create a new directory called `templates` in your theme directory. In it, create a new directory called `partials`. Make sure you end up with a file structure like this:

- templates/
  - partials/
- index.php (empty file)
- functions.php
- style.css

You may guess where this is going. All new template files will be in the `templates` directory, with the partial files being in the `partials` directory. All templates are Handlebars files that end with the `.hbs` file extension.

#### Getting started

In your `functions.php` file, let's define a route. For example, a route for the front page:

```php
$app->isFrontPage(function() use($app) {

  // code that is here will be executed when is_front_page is true

});
```

This might look familiar to you. It's the same conditional you would use in traditional WordPress theme development, except in our case we provide it with a callback function that we execute when the conditional equals true.

Then, to actually display a Handlebars template on the front page of your site, call the template file, like so:

```php
$app->isFrontPage(function() use($app) {

  $app->template('home');

});
```

This would load the template file located at `templates/home.hbs`. In it you can write whatever your heart desires. To pass information down to your template, simply add it to the `template` method, like so:

```php
$app->isFrontPage(function() use($app) {

  $app->template('home', ['name' => 'John']);

});
```

And then you can display it in your `home.hbs` file this:


```handbelars
Hi, {{name}}
```

The rest of it works exactly like it should when it comes to Handlebars templating, so you can refer to [its documentation](https://handlebarsjs.com/guide/) for further help.

#### Partials

Partials live in the `{your-theme}/templates/partials` folder. To register partials for use within your templates, you need to call `registerTemplatePartials`, like so:

```php
$app->registerTemplatePartials([
  'header',
  'sidebar',
  'footer'
]);
```

You can then use these with `{{> header}}`, `{{> sidebar}}` and `{{> footer}}` in your Handlebars templates. You can register template partials within a scope of a route, such as inside the Conditional Callbacks so that the partials are only available within that context, but you can also register them globally if you do it outside the Conditional Callbacks which then would make the partials available globally.

#### Helpers

There are currently these helpers available for use by default:

- `{{info "name"}}` - usage of `get_bloginfo()`
- `{{menu "theme-location"}}` - usage of `wp_nav_menu()` 
- `{{meta "id" "key"}}` - usage of `get_post_meta()`
- `{{date "format"}}` - usage of `date()`
- `{{pagination "Prev" "Next" pages}}` - usage of `paginate_links` (note the `pages` part, it comes from `query and you need to pass this along to pagination)

If you wish to register your own helpers for use within templates, you need to call `registerTemplateHelpers`, like so:

```php
$app->registerTemplateHelpers([
  'greetings' => function($name) {
    return 'Hi, ' . $name;
  }
]);
```

You can use the helpers in your Handlebars template, like with the above example being the basis:

```handlebars
{{greetings "John"}}
```

There can of course be as many attributes as you wish. 

#### Variables

There are currently these variables available for use:

- `wp_head` - prints the `wp_head()`
- `wp_footer` - prints the `wp_footer()`
- `body_class` - prints the `body_class()`
- `language_attributes` - prints the `language_attributes()`


#### Querying content

Since `WP_Query` isn't suitable for usage in a Handlebars template, we have a wrapper over `WP_Query` that returns data in a Handlebars-friendly way. So, let's say you want to display posts in your blog, you can do so like this:

```php
$app->isHome(function() use($app) {

  $app->template('blog', [
    'posts' => $app->query(['post_type' => 'post'])
  ]);

});
```

Then in your `.hbs` file you would be able to do this:

```handlebars
{{#each posts}}

  <h2>{{title}}</h2>

  <div class="entry">{{{content}}}</div>

{{/each}}
```

List of available variables that come with the `query` are the following:

- `id` - post ID
- `title` - post title
- `url` - post URL
- `published_at` - post published date
- `author` - post author
- `image` - post thumbnail array
  - `image.thumbnail` - thumbnail size
  - `image.small` - small size
  - `image.medium` - medium size
  - `image.large` - large size
  - `image.full` - full size
- `has_tag` - a boolean for if the post has a tag or not
- `tags` - an array of tags
  - `id` - tag ID
  - `name` - tag name
  - `url` - tag URL
- `content` - post content
- `pages` - number of pages of this type of content (can be used for pagination)

### Extending

If there aren't enough features in Framework for you, feel free to extend it. You can do so by simply creating your own class
that extends the Framework class, like so:

```php
class App extends Framework {

  // your code here

}
```

This allows you to create as much cool stuff you want that would still all be accessible along with all the other stuff, like so:

```php
$app = new App();

// now you can use $app to access the Framework methods as well as your own.
```

## Using Framework in JavaScript

### Creating AJAX actions

You can create AJAX actions with the `ajaxAction` method, a fully configured example is:

```javascript
framework.ajaxAction('send_message', {
  name: 'John Smith',
  message: 'Hi there!'
}).then(response => {

  // Do something with response

}).catch(error => {

  // Do something with error

})
```

Keep in mind that this expects you to have set up an Ajax Action in the back-end. You can do that with the `ajaxAction` method in PHP, like stated in above documentation. Oh and, all ajaxActions are HTTP POST requests.

## Changelog

### 2.5

- Cleaned up a bunch of code, removed snake_case mirrors, added types. Now only compatible with PHP 7.4.
- Added `input` helper method, for retrieving things in `$_REQUEST`.

### 2.4

- Added `isPostType` and `isPostTypeArchive` conditional callbacks.

### 2.3.1 

- Fixed an issue that caused `isPage` not to work as expected.

### 2.3

- Added `isTax` conditional callback. 
- Added `registerTaxonomy` helper method.

### 2.2

- All the methods now are in camelCase, so it's recommended to use them instead as the snake_case methods will be deprecated in a future release.
- Added new `paged` method for returning the current pagination number.
- The `query` method now also returns the `max_num_pages` as how many pages of this type of content is via the `{{pages}}` variable.
- Added new `pagination` templating helper for displaying pagination in the templates.
- Added new `registerPostType` method for easy registering of custom post types.

### 2.1

- Added `current` method for returning the currently in scope queried object.
- No longer having pre-defined partials for use in templates. Register your partials with `register_template_partials`.
- You can now register your own template helpers by calling `register_template_helpers`.
- Conditional callbacks `is_category` and `is_tag` now expect two arguments, first is $id and the second is callback.
- The first parameter of conditional callbacks (if there are two) expects an asterisk `*` instead of `any`, to match for anything now.
- Added `is_user_logged_in`, `is_author`, `is_search` and `is_404` conditional callbacks.
- Renamed `wp_nav_menu` Handlebars helper to `menu`.
- Renamed `bloginfo` Handlebars helper to `info`.
- Renamed `post_meta` Handlebars helper to `meta`.
- Automatic feed links for posts and pages is now loaded with sensible defaults when calling `init`.

### 2.0.1

- Removed the `{{get_the_date}}` helper in favor of having `{{published_at}}` available with the `query` itself, because `get_the_date` couldn't get the scope of the post and thus displayd the wrong date.

### 2.0

- Framework is now a WordPress plugin, so to install it simply upload the `.zip` file from [releases](https://github.com/digital-baboon/framework/releases) to the admin panel.
- No longer using static methods, so you must first initialize the class with `new` and set it to a variable.
- Added conditional callbacks.
- Added querying content.
- Added templating.

### 1.3

- Added `script` method to the PHP lib for easy adding of scripts.
- Added `style` method to the PHP lib for easy adding of styles.
- Created the JavaScript lib with ability to create Ajax Actions.
- Loading the JavaScript with sensible defaults via `init` in PHP lib.