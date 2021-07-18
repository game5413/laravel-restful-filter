# Laravel RESTful Filter
A simple way to filter your query on RESTful API

# Compatibility
| Framework      | Version                    |
| :------------- | :----------:               |
| Laravel        | `5.*`, `6.*`, `7.*`, `8.*` |
| Lumen          | `5.*`, `6.*`, `7.*`, `8.*` |

# Limitation
- Currently, only working on SQL type Database (already tested on MySQL, PostgreSQL, SQL Server)
- Probably SQL query not optimized (already checked on this but i think it doesn't have, the result was fast. In the end, it's your choise)
- Sorting through relation not available
- Filter `Between` and `In` not available
- Can't add custom logic

# Available Search Operators
 - Less Than
 - Less Than Equal
 - Greater Than
 - Greater Than Equal
 - Equal
 - LIKE
 - NOT

# Installation
Install from composer
```bash
composer install kemodev/laravel-restful-filter
```
Use Package on your Model
```php
<?php

namespace App;

use Kemodev\RestfulFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Filterable;

    //rest of your code on model
}
```

# Get Started
First, you need to define what field that able to filter with your API
## 1. Filter
There are 3 kind available method for filter:
 - Basic
```php
<?php

namespace App;

use Kemodev\RestfulFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Filterable;

    protected $filterableColumns = [
        'email' => 'email'
    ];
    //rest of your code on model
}
```
 - Relation
 ```php
<?php

namespace App;

use Kemodev\RestfulFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Filterable;

    protected $filterableColumns = [
        'email' => 'email',
        'role_name' => 'role.name'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    //rest of your code on model
}
```
 - All of Kind
 ```php
<?php

namespace App;

use Kemodev\RestfulFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Filterable;

    protected $filterableColumns = [
        'email' => 'email',
        'search' => 'first_name,last_name,role.name'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    //rest of your code on model
}
```
This method usually used when using one field filter like on default [datatables](https://datatables.net)

## 2. Sorting
```php
<?php

namespace App;

use Kemodev\RestfulFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Filterable;

    protected $sortableColumns = [
        'email' => 'email'
    ];

    //rest of your code on model
}
```

**If you notice, that variables was associate array with key indicate the query param on URL and value was the column name.**

## 3. Usage
The most basic one, you can use like these
```php
<?php

namespace App\Http\Controllers;

use App\Users;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getAll(Request $request)
    {
        $filter = $request->except('sort');
        $sort = $request->get('sort');

        $data = User::searchable($filter)
            ->sortable($sort)
            ->get();
    }
}
```
with this code, it will assume that you produce url like <br>
[https://your-domain.dev?name=febryan&email=febryan@example.com&sort=name_asc](https://your-domain.dev?name=febryan&email=febryan@example.com&sort=name_asc)

But the `$filter` variables take scope to wide, because you may have another query params that not related for filter your data. The best practice that i assume was write your code like these

```php
<?php

namespace App\Http\Controllers;

use App\Users;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getAll(Request $request)
    {
        $filter = $request->get('filters');
        $sort = $request->get('sort');

        $data = User::searchable($filter)
            ->sortable($sort)
            ->get();
    }
}
```
with this code, it will assume that you produce url like <br>
[https://your-domain.dev?filters[name]=febryan&filters[email]=febryan@example.com&sort=name_asc&signature=123qweasdzxc](https://your-domain.dev?filters[name]=febryan&filters[email]=febryan@example.com&sort=name_asc&signature=123qweasdzxc)

For using available search operator on filter, you can input your query params like these:
- Less Than: `filters[age]=lt:12`
- Less Than Equal: `filters[age]=lte:12`
- Greater Than: `filters[age]=gt:12`
- Greater Than Equal: `filters[age]=gte:12`
- Equal: `filters[name]=John Doe`
- LIKE: `filters[name]=like:john`
- NOT: `filters[name]=not:John Doe`

For change direction on sorting, you can input your query params like these:
- Ascending: `sort=name_asc`
- Descending: `sort=name_desc`

Or you want declare multiple sorting, just type your query params like these:
`sort=name_asc,email_desc`

# Contributing
Any contributions welcome! but if you are busy to contribute or you don't know how to contribute because seeing my ugly code, you can do thing on below to declare your contributions.
## Stars the project
If you find this package useful and you want to encourage me to maintain and work on it, Just press the star button to declare your willing.

## Reward me with a cup of tea 🍵
Send me as much as a cup of tea worth in your country, so I'll have the energy to maintain this package.

<div style="position: relative;">
    <a href="https://paypal.me/febryanph?locale.x=id_ID" style="position: absolute; left: -10px; top: -10px;">
        <img src="https://raw.githubusercontent.com/stefan-niedermann/paypal-donate-button/master/paypal-donate-button.png" width="150"/>
    </a>
</div>

Ethereum: 0x8a43C741519ff8447c033E9D0a49F94A6EB76047
