# Laravel Dotenv Editor
![laravel-dotenv-editor](https://cloud.githubusercontent.com/assets/9862115/25982836/029612b2-370a-11e7-82c5-d9146dc914a1.png)

[![Latest Stable Version](https://poser.pugx.org/akbardwi/laravel-env-editor/v/stable)](https://packagist.org/packages/akbardwi/laravel-env-editor)
[![Total Downloads](https://poser.pugx.org/akbardwi/laravel-env-editor/downloads)](https://packagist.org/packages/akbardwi/laravel-env-editor)
[![Latest Unstable Version](https://poser.pugx.org/akbardwi/laravel-env-editor/v/unstable)](https://packagist.org/packages/akbardwi/laravel-env-editor)
[![License](https://poser.pugx.org/akbardwi/laravel-env-editor/license)](https://packagist.org/packages/akbardwi/laravel-env-editor)

Laravel Dotenv Editor is the .env file editor (or files with same structure and syntax) for Laravel 5.8+. Now you can easily edit .env files with the following features:

- Read raw content of file.
- Read entries of file content.
- Read setters (key-value-pair) in file content.
- Check for existence of setter.
- Append empty lines to file content.
- Append comment lines to file content.
- Append new or update an existing setter entry.
- Update comment of an existing setter entry.
- Update export status of an existing setter entry.
- Delete existing setter entry in file content.
- Backup and restore file content.
- Manage backuped files.

# Versions and compatibility
Laravel Dotenv Editor is compatible with Laravel 5.8 and later.

# Documentation
Look at one of the following topics to learn more about Laravel Dotenv Editor:

- [Installation](#installation)
- [Configuration](#configuration)
    - [Auto backup mode](#auto-backup-mode)
    - [Backup location](#backup-location)
    - [Always create backup folder](#always-create-backup-folder)
- [Usage](#usage)
    - [Working with facade](#working-with-facade)
    - [Using dependency injection](#using-dependency-injection)
    - [Loading file for working](#loading-file-for-working)
    - [Reading file content](#reading-file-content)
    - [Edit file content](#edit-file-content)
    - [Backing up and restoring file](#backing-up-and-restoring-file)
    - [Method chaining](#method-chaining)
    - [Working with Artisan CLI](#working-with-artisan-cli)
    - [Exceptions](#exceptions)

## Installation
You can install this package through [Composer](https://getcomposer.org). At the root of your application directory, run the following command (in any terminal client):

```shell
$ composer require akbardwi/laravel-env-editor
```

## Configuration
To start using the package, you should publish the configuration file so that you can configure the package as needed. To do that, run the following command (in any terminal client) at the root of your application:

```shell
$ php artisan vendor:publish --provider="Akbardwi\LaravelEnvEditor\LaravelEnvEditorServiceProvider" --tag="config"
```

This will create a `config/laravel-dotenv-editor.php` file in your app that you can modify to set your configuration. Also, make sure you check for changes to the original config file in this package between releases. Currently there are the following settings:

#### Auto backup mode
The `autoBackup` setting allows your original file to be backed up automatically before saving. Set it to `true` to agree.

#### Backup location
The `backupPath` setting is used to specify where your file is backed up. This value is a sub path (sub-folder) from the root folder of the project application.

#### Always create backup folder
The `alwaysCreateBackupFolder` setting is used to request that the backup folder always be created, whether or not the backup is performed.

## Usage
### Working with facade
Laravel Dotenv Editor has a facade with the name `Akbardwi\LaravelEnvEditor\Facades\LaravelEnvEditor`. You can perform all operations through this facade.

**Example:**

```php
<?php namespace Your\Namespace;

// ...

use Akbardwi\LaravelEnvEditor\Facades\LaravelEnvEditor;

class YourClass
{
    public function yourMethod()
    {
        $return = LaravelEnvEditor::doSomething();
    }
}
```

### Using dependency injection
This package also supports dependency injection. You can easily inject an instance of the `Akbardwi\LaravelEnvEditor\LaravelEnvEditor` class into your controller or other classes.

**Example:**

```php
<?php namespace App\Http\Controllers;

// ...

use Akbardwi\LaravelEnvEditor\LaravelEnvEditor;

class TestLaravelEnvEditorController extends Controller
{
    protected $editor;

    public function __construct(LaravelEnvEditor $editor)
    {
        $this->editor = $editor;
    }

    public function doSomething()
    {
        $return = $this->editor->doSomething();
    }
}
```

### Loading file for working
By default, the Laravel Dotenv Editor will load the dotenv file that Laravel is reading from in your project. That is, if your Laravel is using the `.env.local` file to store the configuration values, the Laravel Dotenv Editor also loads the content from that file by default.

However, if you want to explicitly specify the files you are going to work with, you should use the `load()` method.

**Method syntax:**

```php
/**
 * Load file for working
 *
 * @param  string|null  $filePath           The file path
 * @param  boolean      $restoreIfNotFound  Restore this file from other file if it's not found
 * @param  string|null  $restorePath        The file path you want to restore from
 *
 * @return LaravelEnvEditor
 */
public function load($filePath = null, $restoreIfNotFound = false, $restorePath = null);
```

**Example:**

```php
// Working with the dotenv file that Laravel is using
$editor = LaravelEnvEditor::load();

// Working with file .env.example in root folder of project
$editor = LaravelEnvEditor::load(base_path('.env.example'));

// Working with file .env.backup in folder storage/laravel-dotenv-editor/backups/
$editor = LaravelEnvEditor::load(storage_path('laravel-dotenv-editor/backups/.env.backup'));
```

**Note:** The `load()` method has three parameters:

- **`$filePath`**: The path to the file you want to work with. Set `null` to work with the file `.env` in the root folder.
- **`$restoreIfNotFound`**: Allows to restore your file if it is not found.
- **`$restorePath`**: The path to the file used to restoring. Set `null` to restore from an older backup file.

### Reading file content
#### Reading raw content.
**Method syntax:**

```php
/**
 * Get raw content of file
 *
 * @return string
 */
public function getContent();
```

**Example:**

```php
$rawContent = LaravelEnvEditor::getContent();
```

#### Reading content by entries.
**Method syntax:**

```php
/**
 * Get all entries from file
 *
 * @return array
 */
public function getEntries(bool $withParsedData = false);
```

**Example:**

```php
$lines = LaravelEnvEditor::getEntries(true);
```

**Note:** This will return an array. Each element in the array consists of the following items:

- Starting line number of entry.
- Raw content of the entry.
- Parsed content of the entry (if the `$withParsedData` is set to `true`), including: type of entry (empty, comment, setter...), key name of setter, value of setter, comment of setter...

#### Reading content by keys
**Method syntax:**

```php
/**
 * Get all or exists given keys in file content
 *
 * @param  array  $keys
 *
 * @return array
 */
public function getKeys($keys = []);
```

**Example:**

```php
// Get all keys
$keys = LaravelEnvEditor::getKeys();

// Only get two given keys if exists
$keys = LaravelEnvEditor::getKeys(['APP_DEBUG', 'APP_URL']);
```

**Note:** This will return an array. Each element in the array consists of the following items:

- Number of the line.
- Key name of the setter.
- Value of the setter.
- Comment of the setter.
- If this key is used for the "export" command or not.

#### Reading data of the specific key
**Method syntax:**

```php
/**
 * Return information of entry matching to a given key in the file content.
 *
 * @throws KeyNotFoundException
 *
 * @return array
 */
public function getKey($key);
```

**Example:**

```php
// Get all keys
$keys = LaravelEnvEditor::getKey('EXAMPLE_KEY');
```

#### Determine if a key exists
**Method syntax:**

```php
/**
 * Check, if a given key is exists in the file content
 *
 * @param  string  $keys
 *
 * @return bool
 */
public function keyExists($key);
```

**Example:**

```php
$keyExists = LaravelEnvEditor::keyExists('APP_URL');
```

#### Get value of a key
**Method syntax:**

```php
/**
 * Return the value matching to a given key in the file content
 *
 * @param  $key
 *
 * @throws KeyNotFoundException
 *
 * @return string
 */
public function getValue($key);
```

**Example:**

```php
$value = LaravelEnvEditor::getValue('APP_URL');
```

### Edit file content
To edit file content, you have two jobs:

- First is writing content into the buffer.
- Second is saving the buffer into the file.

> Always keep in mind that the contents of the buffer and the dotenv file will not be the same unless you have saved the contents.

#### Add an empty line into buffer
**Method syntax:**

```php
/**
 * Add empty line to buffer
 *
 * @return LaravelEnvEditor
 */
public function addEmpty();
```

**Example:**

```php
$editor = LaravelEnvEditor::addEmpty();
```

#### Add a comment line into buffer
**Method syntax:**

```php
/**
 * Add comment line to buffer
 *
 * @param string $comment
 *
 * @return LaravelEnvEditor
 */
public function addComment(string $comment);
```

**Example:**

```php
$editor = LaravelEnvEditor::addComment('This is a comment line');
```

#### Add or update a setter into buffer
**Method syntax:**

```php
/**
 * Set one key to|in the buffer.
 *
 * @param string      $key     Key name of setter
 * @param null|string $value   Value of setter
 * @param null|string $comment Comment of setter
 * @param null|bool   $export  Leading key name by "export "
 *
 * @return LaravelEnvEditor
 */
public function setKey(string $key, ?string $value = null, ?string $comment = null, $export = null);
```

**Example:**

```php
// Set key ENV_KEY with empty value
$editor = LaravelEnvEditor::setKey('ENV_KEY');

// Set key ENV_KEY with none empty value
$editor = LaravelEnvEditor::setKey('ENV_KEY', 'anything you want');

// Set key ENV_KEY with a value and comment
$editor = LaravelEnvEditor::setKey('ENV_KEY', 'anything you want', 'your comment');

// Update key ENV_KEY with a new value and keep earlier comment
$editor = LaravelEnvEditor::setKey('ENV_KEY', 'new value 1');

// Update key ENV_KEY with a new value, keep previous comment and use the 'export' keyword before key name
$editor = LaravelEnvEditor::setKey('ENV_KEY', 'new value', null, true);

// Update key ENV_KEY with a new value, remove comment and keep previous export status
$editor = LaravelEnvEditor::setKey('ENV_KEY', 'new-value-2', '');

// Update key ENV_KEY with a new value, remove comment and export keyword
$editor = LaravelEnvEditor::setKey('ENV_KEY', 'new-value-2', '', false);
```

#### Add or update multi setter into buffer
**Method syntax:**

```php
/**
 * Set many keys to buffer
 *
 * @param  array  $data
 *
 * @return LaravelEnvEditor
 */
public function setKeys($data);
```

**Example:**

```php
$editor = LaravelEnvEditor::setKeys([
    [
        'key'     => 'ENV_KEY_1',
        'value'   => 'your-value-1',
        'comment' => 'your-comment-1',
        'export'  => true
    ],
    [
        'key'     => 'ENV_KEY_2',
        'value'   => 'your-value-2',
        'export'  => true
    ],
    [
        'key'     => 'ENV_KEY_3',
        'value'   => 'your-value-3',
    ]
]);
```

Alternatively, you can also provide an associative array of keys and values:

```php
$editor = LaravelEnvEditor::setKeys([
    'ENV_KEY_1' => 'your-value-1',
    'ENV_KEY_2' => 'your-value-2',
    'ENV_KEY_3' => 'your-value-3',
]);
```

#### Set comment for an existing setter
**Method syntax:**

```php
/**
 * Set the comment for setter.
 *
 * @param string      $key     Key name of setter
 * @param null|string $comment The comment content
 *
 * @return LaravelEnvEditor
 */
public function setSetterComment(string $key, ?string $comment = null);
```

**Example:**

```php
$editor = LaravelEnvEditor::setSetterComment('ENV_KEY', 'new comment');
```

#### Set export status for an existing setter
**Method syntax:**

```php
/**
 * Set the export status for setter.
 *
 * @param string $key   Key name of setter
 * @param bool   $state Leading key name by "export "
 *
 * @return LaravelEnvEditor
 */
public function setExportSetter(string $key, bool $state = true);
```

**Example:**

```php
$editor = LaravelEnvEditor::setExportSetter('ENV_KEY', false);
```

#### Delete a setter entry in buffer
**Method syntax:**

```php
/**
 * Delete on key in buffer
 *
 * @param string $key Key name of setter
 *
 * @return LaravelEnvEditor
 */
public function deleteKey($key);
```

**Example:**

```php
$editor = LaravelEnvEditor::deleteKey('ENV_KEY');
```

#### Delete multi setter entries in buffer
**Method syntax:**

```php
/**
 * Delete many keys in buffer
 *
 * @param  array $keys
 *
 * @return LaravelEnvEditor
 */
public function deleteKeys($keys = []);
```

**Example:**

```php
// Delete two keys
$editor = LaravelEnvEditor::deleteKeys(['ENV_KEY_1', 'ENV_KEY_2']);
```

#### Check if the buffer has changed from dotenv file content
**Method syntax:**

```php
/**
 * Determine if the buffer has changed.
 *
 * @return bool
 */
public function hasChanged();
```

#### Save buffer into file
**Method syntax:**

```php
/**
 * Save buffer to file.
 *
 * @param bool $rebuildBuffer Rebuild buffer from content of dotenv file
 *
 * @return LaravelEnvEditor
 */
public function save(bool $rebuildBuffer = true);
```

**Example:**

```php
$editor = LaravelEnvEditor::save();
```

### Backing up and restoring file
#### Backup your file
**Method syntax:**

```php
/**
 * Create one backup of loaded file
 *
 * @return LaravelEnvEditor
 */
public function backup();
```

**Example:**

```php
$editor = LaravelEnvEditor::backup();
```

#### Get all backup versions
**Method syntax:**

```php
/**
 * Return an array with all available backups
 *
 * @return array
 */
public function getBackups();
```

**Example:**

```php
$backups = LaravelEnvEditor::getBackups();
```

#### Get latest backup version
**Method syntax:**

```php
/**
 * Return the information of the latest backup file
 *
 * @return array
 */
public function getLatestBackup();
```

**Example:**

```php
$latestBackup = LaravelEnvEditor::getLatestBackup();
```

#### Restore your file from latest backup or other file
**Method syntax:**

```php
/**
 * Restore the loaded file from latest backup file or from special file.
 *
 * @param  string|null  $filePath
 *
 * @return LaravelEnvEditor
 */
public function restore($filePath = null);
```

**Example:**

```php
// Restore from latest backup
$editor = LaravelEnvEditor::restore();

// Restore from other file
$editor = LaravelEnvEditor::restore(storage_path('laravel-dotenv-editor/backups/.env.backup_2017_04_10_152709'));
```

#### Delete one backup file
**Method syntax:**

```php
/**
 * Delete the given backup file
 *
 * @param  string  $filePath
 *
 * @return LaravelEnvEditor
 */
public function deleteBackup($filePath);
```

**Example:**

```php
$editor = LaravelEnvEditor::deleteBackup(storage_path('laravel-dotenv-editor/backups/.env.backup_2017_04_10_152709'));
```

#### Delete multi backup files
**Method syntax:**

```php
/**
 * Delete all or the given backup files
 *
 * @param  array  $filePaths
 *
 * @return LaravelEnvEditor
 */
public function deleteBackups($filePaths = []);
```

**Example:**

```php
// Delete two backup file
$editor = LaravelEnvEditor::deleteBackups([
    storage_path('laravel-dotenv-editor/backups/.env.backup_2017_04_10_152709'),
    storage_path('laravel-dotenv-editor/backups/.env.backup_2017_04_11_091552')
]);

// Delete all backup
$editor = LaravelEnvEditor::deleteBackups();
```

#### Change auto backup mode
**Method syntax:**

```php
/**
 * Switching of the auto backup mode
 *
 * @param  boolean  $on
 *
 * @return LaravelEnvEditor
 */
public function autoBackup($on = true);
```

**Example:**

```php
// Enable auto backup
$editor = LaravelEnvEditor::autoBackup(true);

// Disable auto backup
$editor = LaravelEnvEditor::autoBackup(false);
```

### Method chaining
Some functions of loading, writing, backing up, restoring support method chaining. So these functions can be called chained together in a single statement. Example:

```php
$editor = LaravelEnvEditor::load('.env.example')->backup()->setKey('APP_URL', 'http://example.com')->save();

return $editor->getKeys();
```

### Working with Artisan CLI
Now, Laravel Dotenv Editor has 6 commands which can be used easily with the Artisan CLI. These are:

- `php artisan dotenv:backup`
- `php artisan dotenv:get-backups`
- `php artisan dotenv:restore`
- `php artisan dotenv:get-keys`
- `php artisan dotenv:set-key`
- `php artisan dotenv:delete-key`

Please use each of the commands with the `--help` option to leanr more about there usage.

**Example:**

```shell
$ php artisan dotenv:get-backups --help
```

### Exceptions
This package will throw exceptions if something goes wrong. This way it's easier to debug your code using this package or to handle the error based on the type of exceptions.

| Exception                    | Reason                                         |
| ---------------------------- | ---------------------------------------------- |
| *FileNotFoundException*      | When the file was not found.                   |
| *InvalidKeyException*        | When the key of setter is invalid.             |
| *InvalidValueException*      | When the value of setter is invalid.           |
| *KeyNotFoundException*       | When the requested key does not exist in file. |
| *NoBackupAvailableException* | When no backup file exists.                    |
| *UnableReadFileException*    | When unable to read the file.                  |
| *UnableWriteToFileException* | When unable to write to the file.              |

# Contributors
This project exists thanks to all its [contributors](https://github.com/Akbardwi/Laravel-Dotenv-Editor/graphs/contributors).

# License
[MIT](LICENSE) © Akbar Dwi Syahputra
