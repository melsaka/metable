# Metable

Enhance your Laravel applications with flexible meta data management. Simplify your data structure, streamline access, and customize your meta tables effortlessly.

## Introduction

Metable simplifies storing extensive model data without adding extra database columns. 

Useful for managing diverse information in Laravel models like blog posts, it avoids column clutter and enables storing unique data efficiently.

## Features

- **Effortless Data Management:** Utilize getters and setters to seamlessly access, add, edit, or delete meta data for each model.

- **Streamlined Database Structure:** Escape the clutter of creating numerous columns in your database table with our efficient alternative.

- **Advanced Sorting & Filtering:** Elevate your model records by effortlessly ordering and filtering them based on their meta attributes.

- **Custom Meta Tables:** Easily create and associate custom meta tables with their respective models, offering flexibility and organization.

- **Versatile Meta Types:** Experience support for a wide range of meta types, including `collection`, `model`, `object`, `array`, `JSON`, `string`, `integer`, `double`, `boolean`, and `null`. Your data, your way!

## Installation

Require this package with Composer:

```php
composer require melsaka/metable
```

Register the package's service provider in config/app.php:

```php
'providers' => [
    ...
    Melsaka\MediaFile\MetableServiceProvider::class,
    ...
];
```

Execute the migration command to create the meta table:

```php
php artisan migrate
```

To add meta functionality to your models, simply use the Metable trait:

```php
use Melsaka\Metable\Metable;

class Post extends Model
{
    use Metable;
    ...
}
```

### Optional Configuration

You can publish the meta configuration file using the following command:

```php
php artisan vendor:publish --tag=metable
```

In this file, you can customize the default meta table (default: `meta`) or specify a custom meta table for a specific model.

## Usage

Now, you can harness the power of Metable to manage your data efficiently. 

In the examples below, we assume that the `$post` variable is set to `Post::first()`, where `Post` serves as an example model.

### Basic Methods

**Create New Meta**

To create new meta data, use the `createMeta` method on your model:

```php
// Create a single meta entry
$post->createMeta('key', 'value');

// Alias for createMeta
$post->addMeta('key1', 'value1');

// Create multiple meta entries at once
$post->createMeta([
    'key2' => 'value2',
    'key3' => 'value3'
]);
```

**Return:** The **createMeta** method returns `true` if the creation is successful, otherwise `false`.

The `addMeta` method is an alias for `createMeta`.

Alternatively, you can create meta using the `meta` property:

```php
$post->meta->add('key', 'value');
```

**Update Meta**

To update existing meta data, use the `updateMeta` method on your model:

```php
// Update a single meta entry
$post->updateMeta('key', 'new value');

// Alias for updateMeta
$post->editMeta('key1', 'new value1');

// Update multiple meta entries at once
$post->updateMeta([
    'key2' => 'new value 2',
    'key3' => 'new value 3'
]);
```

**Return:** The **updateMeta** method returns `true` if the update is successful, If the meta does not exist or the update fails, it returns `false`.

The `editMeta` method is an alias for `createMeta`.

Alternatively, you can update meta using the `meta` property:

```php
$post->meta->edit('key', 'value');
```

**Create or Update Meta**

Use the `setMeta` method to either create new meta or update existing ones:

```php
// Create a new meta entry if it doesn't exist, or update if it does
$post->setMeta('key', 'value'); // Create meta

$post->setMeta('key', 'new value'); // Update meta

// Set multiple meta entries at once
$post->setMeta([
    'key1' => 'value 1',
    'key2' => 'value 2'
]);
```

**Return:** The **setMeta** method returns `true` if it is successful; otherwise, it returns `false`.

Alternatively, you can set meta using the `meta` property:

```php
$post->meta->set('key', 'value');

// or

$post->meta->key1 = 'value';

$post->meta->key2 = 'value2';

$post->meta->save();
```

**Get Meta** 

Retrieve meta data using the `getMeta` method:

```php
// Return the meta value or null if it doesn't exist
$post->getMeta('key');

// Return the value or a default value if it doesn't exist or is null
$post->getMeta('key', 'default value');
```

You can also access meta values using the `meta` property:

```php
 // Return the meta value
$post->meta->key;

// Return the value or a default value if it doesn't exist or is null
$post->meta->get('key', 'default value'); 
```

**Get All Meta**

Use the `getAllMeta` method to retrieve all meta data as a collection:

```php
// Return a collection of all metas for this model
// If no meta data exists, an empty collection is returned
$post->getAllMeta();

// or

$post->meta->all();
```

**Delete Meta**

To delete meta data, use the `deleteMeta` method:

```php
// Delete all meta data for this model
$post->deleteAllMeta();

// Or

$post->meta->deleteAll();


// Delete a specific meta entry
$post->deleteMeta('key');

// Or

$post->meta->delete('key');
```

You can also use the `removeMeta` method, which has the same functionality. The `deleteMeta` method:

```php
// Delete a specific meta entry
$post->removeMeta('key');
```

**Check Meta Existence**

You can use the `hasMeta` method to check if a particular meta exists (returns `true`), or use the second argument to determine whether null values are accepted (returns `true` even if the value is null):

```php
// Check if this model has at least one meta
$post->hasMeta();

// Check if a specific meta exists (returns true or false)
$post->hasMeta('key');

// Specify to accept null values (returns true if value is null)
$post->hasMeta('key', true);
```
Alternatively, you can set meta using the `meta` property:

```php
$post->meta->has('key', true);
```

**Increase Meta**

Increase a meta value using the `increaseMeta` method:

```php
// Set the meta key to an integer value
$post->setMeta('key', 3);

// Increase the meta value by 3 (meta value becomes 6)
$post->increaseMeta('key', 3);
```

**Decrease Meta**

Decrease a meta value using the `decreaseMeta` method:

```php
// Set the meta key to an integer value
$post->setMeta('key', 3);

// Decrease the meta value by 1 (meta value becomes 2)
$post->decreaseMeta('key', 1);
```

### Clauses

**Order By Meta**

Sort your database results using the orderByMeta clause:

```php
// Sort by 'price' meta in ascending order
Product::orderByMeta('price')->get();

// Sort by 'price' meta in descending order
Product::orderByMeta('price', 'desc')->get();

// Multiple sorting criteria
Product::orderByMeta('price')
    ->orderByMeta('reviews', 'desc')
    ->get();
```

**Where Meta Clause**

Filter your items based on meta values using the whereMeta clause:

```php
// Filter items where 'key' equals 'value'
$result = Post::whereMeta('key', 'value');

// Use operators for more complex filtering
$result = Post::whereMeta('key', '>', 100);

// Combine multiple whereMeta clauses
$result = Post::whereMeta('key', '>', 100)
              ->whereMeta('key', '<', 200);

// Use 'orWhereMeta' for additional OR conditions
$result = Post::whereMeta('key', '>', 100)
              ->orWhereMeta('key', '<', 50);

$result = Post::whereMeta([
    ['key1', 'value1'],
    ['key2', '>', 5],
    ['key3', '<>', 'value3'] // where key3 not equal value3
]);
```

For multiple OR conditions, use orWhere:

```php
$result = Post::orWhereMeta([
    ['key1', 'value1'],
    ['key2', '>', 5],
    ['key3', 'value3']
]);
```

**Where Meta In Clause**

Use the whereMetaIn and whereMetaNotIn clauses:

```php
$result = Post::whereMetaIn('key', ['value1', 'value2']);
$result = Post::whereMetaNotIn('key', ['value1', 'value2']);

// Multiple clauses
$result = Post::whereMetaIn('key', [1, 2])
              ->whereMetaIn('key2', [1, 2]);

// 'orWhere' clauses
$result = Post::whereMetaNotIn('key', [1, 2, 3])
              ->orWhereMetaIn('key2', [1, 2])
              ->orWhereMetaNotIn('key3', [1, 2]);
```

**Where Meta Null Clause**

Use whereMetaNull and whereMetaNotNull clauses:

```php
$result = Post::whereMetaNull('key');
$result = Post::whereMetaNotNull('key');

// Multiple clauses
$result = Post::whereMetaNull('key')
              ->whereMetaNull('key2');

// 'orWhere' clauses
$result = Post::whereMetaNotNull('key')
              ->orWhereMetaNull('key2')
              ->orWhereMetaNotNull('key3');
```

**Where Meta Has Clause**

Filter records based on the presence of meta data with whereMetaHas and whereMetaDoesntHave clauses:

```php
// Filter records that have at least one meta
$result = Post::whereMetaHas();

// Filter records that have a specific meta key
$result = Post::whereMetaHas('key');
$result = Post::whereMetaHas('key', true); // Count null values as well

// Filter records that don't have a specific meta key
$result = Post::whereMetaDoesntHave('key');
$result = Post::whereMetaDoesntHave('key', true); // Count null values as well

// Multiple clauses
$result = Post::whereMetaHas('key')
              ->whereMetaDoesntHave('key2');

// 'orWhere' clauses
$result = Post::whereMetaDoesntHave('key')
              ->orWhereMetaHas('key2')
              ->orWhereMetaDoesntHave('key3');
```

**Eager Loading**

When you call `$post->getMeta('key')` for the first time, all meta data for that model is loaded once. Subsequent calls to get meta from the same model do not execute additional queries. However, if you try to get meta from a different model, a new query will be executed.

To load all meta data for all models in a single query, use eager loading with the `withMeta` scope:

```php
$posts = Post::withMeta()->get(); // Returns all post results with their meta values
```

**Note** that using `with('meta')` will not work for eager loading.

If you wanna access the relationship to do some custom queries you can do so using `$post->metaQuery()` for ex:

```php
    $post->metaQuery()->where('key', 'key1')->get();
```

And to do custom `whereHas` query you can do it like this:

```php
    $post->whereHas(Post::metaRelationName(),  function ($query) {
        $query->where('key', 'key1');
        ...
    });
```

### Other Methods And Features

**Notes**

All `collect([])`, `[]`, `"{}"`, `"[]"`, `""`, and null values will be stored in database as `null`. 

If one of the items in the metable model is deleted, all the meta related to that item will be deleted too.

**Data Type**

Available data types: `collection`, `model`, `object`, `array`, `JSON`, `string`, `integer`, `double`, `boolean`, and `null`.

**Custom Meta Table**

By default, all meta-data for all models will be stored in the `meta` database table. However, if you want to use a separate table for a particular model, follow these steps:

Publish the package config file using this command:

```php
php artisan vendor:publish --tag=metable
```

This command will place a file named `metable.php` in your config folder.

Open the config file. In the `tables` array, you'll find a `custom` array. You can add new table names to this array. For example, to handle users' meta, add `users_meta` to this array:

```php
'tables' => [
    'default' => 'meta',
    'custom'  => [
        'users_meta'
    ],
]
```

Run the migrate command to create the new table:

```php
php artisan migrate
```

If you have already migrated the Meta migration, you should rollback these migrations and migrate them again to create the new tables.

Now that the new table is created, you can specify it in the User model (or any model you want) to handle its meta with this table:

```php
use Melsaka\Metable\Metable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Metable;

    protected $metableTable = 'users_meta';
}
```

**Meta Model**

You are free to use the meta model in your project:

```php
use Melsaka\Metable\Models\Meta;

$count = Meta::count();
```

**Meta Table**

The structure of the meta table is as follows:

```php
Schema::create('meta', function (Blueprint $table) {
    $table->id();
    $table->integer('parent_id')->unsigned();
    $table->string('parent_type');
    $table->string('key');
    $table->text('value');
    $table->string('type')->default('string');

    $table->index(['parent_type', 'parent_id']);
    $table->index(['parent_type', 'parent_id', 'key']);
    $table->unique(['parent_type', 'parent_id', 'key']);
    $table->timestamps();
});
```

## License

This package is released under the MIT license (MIT).