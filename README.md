HasMeta
=======

A Laravel trait to access model meta data as if it was a property on your model

Installation
============

Run `composer require jaybizzle/hasmeta 0.1.*` or add `"jaybizzle/hasmeta": "0.1.*"` to your `composer.json` file

In the Model that you want to utilise `HasMeta` add the following properties

```PHP
	use Jaybizzle\Hasmeta\HasMetaTrait;

	protected $meta_model       = 'ModelName'; // the name of your meta data model
	protected $meta_foreign_key = 'user_id'; // the foreign key of your main model
	protected $meta_primary_key = 'meta_id'; // the primary key of you meta data model
	protected $meta_key_name    = 'dataName'; // the column name that stores your meta data key name
	protected $meta_value_name  = 'dataValue'; // the column name that stores you meta data value
```

Real World Example
==================

Nothing like a simple example to explain things

###Setup

`users` table


| id  | email | password |
| ------------- | ------------- | ------------- |
| 1  | john@somewhere.com  | ADpQeKh$2y$10$0dh/BerzTrEOBhu4SR3w05  |
| 2  | sam@somewhere.com  | pQeKhrzTrEOBhu$2y$10$0dh/BeAD4SR3w05  |
| 3  | neil@somewhere.com  | ADpQeKhrzTrEOBhu4SR3w05$2y$10$0dh/Be  |
| 4  | ben@somewhere.com  | BeADpQeKhrzTrEO$2y$10$0dh/Bhu4SR3w05  |
| 5  | mark@somewhere.com  | DpQeKhrzTrEOBhu4SR3w05$2y$10$0dh/BeA  |

`users_meta` table

| id  | user_id | meta_name | meta_value
| ------------- | ------------- | ------------- | ------------- |
| 1  | 1  | first_name  | John |
| 2  | 1  | surname  | Roberts |
| 3  | 1  | age  | 20 |
| 4  | 1  | gender  | Male |
| 5  | 2  | first_name  | Steven |
| 6  | 2  | surname  | Watson |
| 7  | 2  | age  | 35 |
| 8  | 2  | gender  | Male |
| 9  | 3  | first_name  | Sam |
| 10  | 3  | surname  | Faddy |
| 11  | 3  | age  | 30 |
| 12  | 3  | gender  | Female |
| 13  | 4  | first_name  | Ben |
| 14  | 4  | surname  | Prokop |
| 15  | 4  | age  | 32 |
| 16  | 4  | gender  | Male |
| 17  | 5  | first_name  | Jo |
| 18  | 5  | surname  | Blair |
| 19  | 5  | age  | 31 |
| 20  | 5  | gender  | Female |


`User.php` model

```PHP
class User extends Eloquent {

	use Jaybizzle\Hasmeta\HasMetaTrait;

	protected $meta_model       = 'UserMeta';
	protected $meta_foreign_key = 'user_id';
	protected $meta_primary_key = 'id';
	protected $meta_key_name    = 'meta_name';
	protected $meta_value_name  = 'meta_value';


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The primary key on the table
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';
	
	// ...
}
```
`UserMeta.php` model

```PHP
class UserMeta extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users_meta';

	/**
	 * The primary key on the table
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';

	// ...
}
```

###Usage

Now we can simply do this for getting meta data...

```PHP
$user = User::find(1);
echo $user->gender; // Will output 'Male'
```

We can save meta data easily too...

```PHP
$user = User::find(1);
$user->gender = 'Female';
$user->save();
```

Delete meta...

```PHP
$user = User::find(1);
$user->gender = null;
$user->save();
```

New meta...

Delete meta...

```PHP
$user = User::find(1);
$user->anything_you_want = 'some lovely value';
$user->save();
```
