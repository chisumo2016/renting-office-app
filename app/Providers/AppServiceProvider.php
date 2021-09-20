<?php

namespace App\Providers;

use App\Models\Office;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use PhpParser\Builder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      Model::unguard();
      Relation::enforceMorphMap([
          'office' => Office::class, //Switch to using custom Polymorhic
          'user' => User::class, //Switch to using custom Polymorhic
        ]);
    }
}
