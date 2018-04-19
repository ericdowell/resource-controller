# Laravel Resource Controller
[![CircleCI](https://circleci.com/gh/ericdowell/resource-controller.svg?style=svg)](https://circleci.com/gh/ericdowell/resource-controller)
[![StyleCI](https://styleci.io/repos/130137009/shield?branch=master)](https://styleci.io/repos/130137009)
[![Maintainability](https://api.codeclimate.com/v1/badges/9667d6f991e0b1573e99/maintainability)](https://codeclimate.com/github/ericdowell/resource-controller/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/9667d6f991e0b1573e99/test_coverage)](https://codeclimate.com/github/ericdowell/resource-controller/test_coverage)
[![Issue Count](https://codeclimate.com/github/ericdowell/resource-controller/badges/issue_count.svg)](https://codeclimate.com/github/ericdowell/resource-controller)


[![License](https://poser.pugx.org/ericdowell/resource-controller/license?format=flat-square)](https://packagist.org/packages/ericdowell/resource-controller)
[![Latest Stable Version](https://poser.pugx.org/ericdowell/resource-controller/version?format=flat-square)](https://packagist.org/packages/ericdowell/resource-controller)
[![Latest Unstable Version](https://poser.pugx.org/ericdowell/resource-controller/v/unstable?format=flat-square)](https://packagist.org/packages/ericdowell/resource-controller)
[![Total Downloads](https://poser.pugx.org/ericdowell/resource-controller/downloads?format=flat-square)](https://packagist.org/packages/ericdowell/resource-controller)

## Installation
To install through composer by running:
```bash
composer require ericdowell/resource-controller 1.0.*
```

## Quick Start
This package assumes that routes are not prefixed and that templates are located in the following locations:
- resources/views/{$model_singular}/create.blade.php
- resources/views/{$model_singular}/edit.blade.php
- resources/views/{$model_singular}/index.blade.php
- resources/views/{$model_singular}/show.blade.php

## Example Setup
Here's an example of the base model that will `morph` to your models.
You can create it with the following command:
```bash
php artisan make:model Text --all
```

We can generate `Post`, one of the models `Text` will morph to by running:
```bash
php artisan make:model Post --all
```

### Database Migrations
Go to the following file and update it to contain the following:
**database/migrations/2018_04_19_021938_create_texts_table.php**
```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('texts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->morphs('text');
            $table->integer('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('texts');
    }
}
```

Go to the following file and update it to contain the following:
**database/migrations/2018_04_19_021947_create_posts_table.php**
```php
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('body');
            $table->integer('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
```

### Models
Go to the following file and add the following:
**app/Text.php**
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text_type',
        'text_id',
        'user_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function text()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

Go to the following file and add the following:
**app/Post.php**
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'body',
        'user_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### AppServiceProvider
Go to the following file and add the following to the boot method:
**app/Providers/AppServiceProvider.php**
```php
<?php

namespace App\Providers;

use App\Post;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'post' => Post::class,
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
```

### Routes:
Go to the following file and add the following:
**routes/web.php**
```php
<?php

/*
 * Models
 */
Route::resource('post', 'PostController');
```

### Controllers
Go to the following file and update it to contain the following:
**app/Http/Controllers/TextController.php**
```php
<?php

namespace App\Http\Controllers;

use App\Text;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use EricDowell\ResourceController\Http\Controllers\ModelMorphController;

abstract class TextController extends ModelMorphController
{
    /**
     * @var string
     */
    protected $morphModel = Text::class;

    /**
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @return array
     */
    protected function beforeStoreModel(FormRequest $request): array
    {
        return [
            'user_id' => $request->input('user_id'),
        ];
    }

    /**
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param \Illuminate\Database\Eloquent\Model $instance
     */
    protected function beforeModelUpdate(FormRequest $request, Model &$instance): void
    {
        // Optional
    }
}
```

I won't go over how to use a [Form Request Validation](https://laravel.com/docs/5.6/validation#form-request-validation)
just note that is what I am referencing in the case of `PostRequest`.

Go to the following file and update it to contain the following:
**app/Http/Controllers/PostController.php**
```php
<?php

namespace App\Http\Controllers;

use App\Post;
use App\Http\Requests\PostRequest;
use Illuminate\Http\RedirectResponse;

class PostController extends TextController
{
    /**
     * Name of the affected Eloquent model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\PostRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PostRequest $request): RedirectResponse
    {
        return $this->storeModel($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\PostRequest $request
     * @param  mixed $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PostRequest $request, $id): RedirectResponse
    {
        return $this->updateModel($request, $id);
    }
}
```

### Views
Coming soon.
