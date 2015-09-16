# Make your eloquent Model loggable

This package provide a simple way to make your Eloquent's model loggable. It works like the Gedmo/Loggable extension for doctrine.

## Installation

Run :

```
composer require devitek/eloquent-loggable
```

Into your `config/app.php` add the service provider :

```
'providers' => [
    // Other Service Providers

    Devitek\Laravel\Eloquent\Loggable\EloquentLoggableServiceProvider::class,
],
```

Get the migration :

```
php artisan vendor:publish --provider="Devitek\Laravel\Eloquent\Loggable\EloquentLoggableServiceProvider" --tag="migrations"
```

and then run :

```
php artisan migrate
```

## How to use it

In your Eloquent model add :

```php
<?php

use \Devitek\Laravel\Eloquent\Loggable;

class MyModel extends Model
{
    use Loggable;

    protected $versioned = [
        'name',
        'other_field',
        'another_field',
        'again_another_field',
    ];
}
```

Now, each time you'll persist your model, all fields that are declared in the `versioned` property will be checked (if dirty) and then logged.

## Get log entries

You now have a method on your model : `logEntries` which is a morphTo relationship.

```php
$logEntries = $model->logEntries();

foreach ($logEntries as $logEntry) {
    /**
     * LogEntry object :
     * action, logged_at, object_id, version, reason, data (as json), user_id
     */
}
```

## Revert

You can revert your model to a previous state using the `revert` method like so :

```php
$model = MyModel::find($id);

$model->revert(); // Revert to the first revision
$model->revert(5); // Revert to the 5th revision
```

Enjoy it ! Feel free to fork :) !