# Generating the Custom Validator

```bash
php bin/console make:validator
```

## Usage

```php
use App\Validator\IsValidOwner;

class CheeseListing
{
  /**
   * @IsValidOwner()
   */
  private $owner;
}
```
