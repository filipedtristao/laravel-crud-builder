# Create and update Eloquent models from API Requests
This package allows you to create and update your Eloquent model and sync relationships based on a request. Request parameter names follow the [JSON API specification](http://jsonapi.org/) as closely as possible.

## Basic usage

### Create a model based on a Request: 

Request body:
```javascript
{
  data: {
    attributes: {
      name: 'Paul',
      age: 77
    }
  }
}
```

PHP controller code:
```php
  use CrudBuilder\CrudBuilder;
    
  $singer = CrudBuilder::for(Singer::class)
    ->allowedAttributes('age', 'name')
    ->create();
  
  //A singer is created in database with the request data 
```

### Update a model based on a Request: 

Request body:
```javascript
{
  data: {
    id: 1,
    attributes: {
      name: 'Paul',
      age: 77
    }
  }
}
```

PHP controller code:
```php
  use CrudBuilder\CrudBuilder;
    
  $singer = CrudBuilder::for(Singer::class)
    ->allowedAttributes('age', 'name')
    ->update();
  
  //A singer with the requested id is updated in database with the request data 
```

Is sure possible to create or update based on the presence of ID in the Request with the `->createOrUpdate()` method.

### Update a model based on a Request, ignoring some attributes:

Request body:
```javascript
{
  data: {
    id: 1,
    attributes: {
      name: 'Paul',
      surname: 'McCartney',
      age: 77
    }
  }
}
```

PHP controller code:
```php
  use CrudBuilder\CrudBuilder;
    
  $singer = CrudBuilder::for(Singer::class)
    ->ignoreAttributes('age', 'surname')
    ->allowedAttributes('name')
    ->update();
  
  //A singer with the requested id is updated in database with the request data, except the ignored attributes 
```

### Update a model based on a Request, including relationships:
Request body:
```javascript
{
  data: {
    id: 1,
    attributes: {
      name: 'Paul'
    },
    relationships: {
      band: {
        data: {
          id: 1
        }
      }
    }
  }
}
```
PHP model code:
```php
class SingerModel extends Model
{
  public function band()
  {
      return $this->belongsTo(Band::class);
  }
}
```

PHP controller code:
```php
  use CrudBuilder\CrudBuilder;
    
  $singer = CrudBuilder::for(Singer::class)
    ->allowedAttributes('name')
    ->allowedRelations('band')
    ->update();
  
  //A singer with the requested id is updated in database with the request data, including the relationship 
```

<b>Note:</b> The only supported relationships so far are: BelongsTo and HasMany.
