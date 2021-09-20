# Laravel Model Hashid

A package to generate model hash id from the model auto increment id for laravel models

## Installation

Require the package using composer:

```bash
composer require touhidurabir/laravel-model-hashid
```

To publish the config file:
```bash
php artisan vendor:publish --provider="Touhidurabir\ModelHashid\ModelHashidServiceProvider" --tag=config
```

## How it can be helpful?

When developing public APIs, sometimes we need to provide data based on givem model id from the requester endpoint . For example 

```
/some-end-point/some-model-resource/{id}
```

where the give **{id}** can be model table associated auto increament id . Using an **uuid** is one another and now a days a pretty popular appraoch to obsecure the model table **id**. such as 

```
/some-end-point/some-model-resource/{uuid}
```

But even with **uuid**, we do need to make adjustment to query to find model resource based on uuid or make the uuid the **Primary Key** of the model. 

However this packaga take a different approach where on model resource creation , it gegerate and store an unique hash id from the model id and then use that to pass as response to remote request . And the requester use that hashid to make request to APIs instead of id or uuid but applying the middlewares, those hashid in the route param or request param dehashed to original model id . So basically this happens, 

```php
$id = 1;
$hashid = 'jRlef2';
```

when making the api request, like this 

```
/some-end-point/some-model-resource/{hashid}
```

Say that route url point to some controller method, then 

```php
class SomeController extends Controller {

    public function show(int $id) {

        // $id where will be 1, not jRlef2
    }
}
```

## Config Options

The published config file contails few possible configuration options . See and read through the **config/hasher.php** to get to know all the possible options . But few important ones are 

### enable
```php
'enable' => env('ID_HASHING', false),
```
Determine if Hashid should be enabled or not . By default it is set to **true**.


### key
```php
'key' => env('ID_HASHING_KEY', ''),
```
Use this unique key as the base or salt to generate the hash . It is not an required details but it is highly recommened to use one unique key through out the app to make the hashid stronger . 


### column
```php
'column' => 'hash_id',
```
The define which column by default this package should look for hashid to retrive or store the gererated one. But still possible to have some column name here and then have something different in some models. 


### alphabets
```php
'alphabets' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
```
This defined the only characters that will be present in a hash string. Best To have along range of characters which by default is lower case a-z with  upper case A-Z and 0-9. 

>NOTE : it must be at least 16 characters long and must only contains unique characters. no duplicate allowed like 'aaaabbbbbbcc' etc .


### regeneration_job
```php
'regeneration_job' => \Touhidurabir\ModelHashid\Jobs\ModelHashidRegeneratorJob::class,
```
This defined the job that will be used to run the update or fill up the missing hash id through the **hashid:run** command. 

## Command

This packaga includes a command that can use to set up the model hash id for the missing ones or update existing one . It will be helpful if this package included later in any laravel app that already have some data in it's tables and needed to set up hash id for those records. To utlize this command run

```bash
php artisan hashid:run User,Profile
```

The one **argument** it reuired is the name of the models command seperated(if there is multiple models to run for) . Behind the scene it calls a queue job to go through the model recored and work on those to update/fill hash id column value. Other options as follow

### --path=
By default it assumes all the models are located in the **App\Models\\** namespace . But if it's located some where else , use the option to define the proper model space path with **trailing slash** .

### --update-all
By default this command will only work with those model records that have the defined hash id column null . So basically it will fill up the missing ones , but if this false is provided with the command it will update all regardless of hashid associated with or not.

### --on-job
This defined if this will update/fill missing one through a queue job . The command use a job where the main logic resides in . But by default it uses the framework provided **dispatchNow** method to run the jon in sync way . if the falg provided and queue configured properly, it will push the job in the queue . 

### --job= 
If need to pass custom queue job implementation, it can be directly provided though this option . also one can update the queue class in the config file . 


## Usage

Use the trait **IdHashable** in model where uuid needed to attach

```php
use Touhidurabir\ModelHashid\IdHashable;
use Illuminate\Database\Eloquent\Model;

class User extends Model {
    
    use IdHashable;
}
```

By default this package use the column name **hash_id** to store the hash value of the model auto increment id.but this can be changed

Also possible to override the uuid column and attaching event from each of the model . to do that need to place the following method in the model : 

```php
use Touhidurabir\ModelHashid\IdHashable;
use Illuminate\Database\Eloquent\Model;

class User extends Model {
    
    use IdHashable;

    /**
     * Get the name of hash column name
     *
     * @return string
     */
    public function getHashColumn() {

        return 'hash';
    }
}
```

Now to make hashed route or request params automatically, use this following 2 middlewares
```php
Touhidurabir\ModelHashid\Http\Middleware\DehashRequestParams // this to dehash request post/get params
Touhidurabir\ModelHashid\Http\Middleware\DehashRouteParams // this to dehash route hash params such as /{id} as hash
```

Register these middlewares in **Http\Kernel.php** file or in controller/route file separately as require .

**NOTE** that the **DehashRequestParams** middleware can dehash request param that is **String(simple hash string)**  or **Array(array of hash string)** only. So if require to handle complex param such as **JSON** string, need to handle that manually. 

To handle such case where one need to decode some keys form a JSON response or for some other purpose need manual dehashing, this package provide 2 helper methods 
- decode_hashid
- decode_hashids

As the name suggest, the **decode_hashid** can only work with a single hash where **decode_hashids** can work with single hash or array of hash. 

Make sure to the put the hashid column name in migration file

```php
$table->string('hash_id')->nullable()->unique()->index();
```
Or can be used with the combination of hasher config as such:

```php
$table->string(config('hasher.column'))->nullable()->unique()->index();
```

This package also include some helper method that make it easy to find model records via UUID. for example

```php
User::byHashId($hash)->where('active', true)->first(); // single hash id
User::byHashId([$hash1, $hash2])->where('active', true)->get(); // multiple hash id
```

Or simple and direct find

```php
User::findByHashId($uhashuid); // single hash id
User::findByHashId([$hash1, $hash2]); // multiple hash id
```

> The package also provide a bit of safe guard by checking if the model table has the given hash id column . 
>
> If the hash id column not found for model table schema, it will not create and attach an hash id.

## Extras

### For API resource
One big use case of this package for the development of API services where gthe developers do not want to include the model original auto incrementing **id** . For that case, one should use the hash_id as such 

```php
class User extends JsonResource {
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {

        return [
            'id'    => $this->hash_id,
            'email' => $this->email,
            ...
        ];
    }
}
```

The above approach is perfectly fine. Howeever as the hash id is not just used mostly for any other model related purpose , one may want to make it hidden like 

```php
/**
 * The attributes that should be hidden for arrays.
 *
 * @var array
 */
protected $hidden = [
    'hash_id',
];
```

For such case, this package includes a simple trait to use with the api resource classes, 

```php
use Touhidurabir\ModelHashid\IdHashing;

class User extends JsonResource {

    use IdHashing;
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {

        return [
            'id'    => $this->getId(),
            'email' => $this->email,
            ...
        ];
    }
}
```

### Using the core Hasher class

The core of this package is the **Hasher** class that handle the whole hash **decode/encode** process. This is hightly dependent on the popular php [Hashid Package](https://github.com/vinkla/hashids) with some bit of extra functionality . One can also use this hasher as for their need fits . To see the details of the hasher class and how it works, check the code itself at **Touhidurabir\ModelHashid\Hasher\Hasher** . 

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](./LICENSE.md)
