[![Latest Stable Version](https://poser.pugx.org/miladrahimi/php-jwt/v/stable)](https://packagist.org/packages/miladrahimi/php-jwt)
[![Total Downloads](https://poser.pugx.org/miladrahimi/php-jwt/downloads)](https://packagist.org/packages/miladrahimi/php-jwt)
[![Build Status](https://travis-ci.org/miladrahimi/php-jwt.svg?branch=master)](https://travis-ci.org/miladrahimi/php-jwt)
[![Coverage Status](https://coveralls.io/repos/github/miladrahimi/php-jwt/badge.svg?branch=master)](https://coveralls.io/github/miladrahimi/php-jwt?branch=master)
[![License](https://poser.pugx.org/miladrahimi/php-jwt/license)](https://packagist.org/packages/miladrahimi/php-jwt)

# PHP-JWT

PHP-JWT is a package written in PHP programming language to encode (generate), decode (parse), verify and validate JWTs 
(JSON Web Tokens). It provides a fluent, easy-to-use, and object-oriented interface.

Confirmed by [JWT.io](https://jwt.io).

## Documentation

### Installation

Add the package to your Composer dependencies with the following command:

```bash
composer require miladrahimi/php-jwt "2.*"
```

Now, you are ready to use the package!

### What is JWT?

In case you are unfamiliar with JWT you can read [Wikipedia](https://en.wikipedia.org/wiki/JSON_Web_Token) or 
[JWT.io](https://jwt.io).

### Simple example

The following example shows how to generate a JWT using the HS256 algorithm and parse it.

```php
use MiladRahimi\Jwt\Generator;
use MiladRahimi\Jwt\Parser;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;

// Signer and verifier is the same HS256
$signer = new HS256('12345678901234567890123456789012');

// Generate a token
$generator = new Generator($signer);
$jwt = $generator->generate(['id' => 666, 'is-admin' => true]);

// Parse the token
$parser = new Parser($signer);
$claims = $parser->parse($jwt);

echo $claims; // ['id' => 666, 'is-admin' => true]
```

### HMAC Algorithms

HMAC algorithms are symmetric, the same algorithm can both sign and verify JWTs. This package supports HS256, HS384, and HS512 of HMAC algorithms. The example mentioned above demonstrates how to use an HMAC algorithm to sign and verify a JWT.

### RSA Algorithms

RSA algorithms are asymmetric. A paired key is needed to sign and verify tokens. To sign a JWT, we use a private key, and to verify it, we use the related public key. These algorithms are useful when the authentication server cannot trust resource owners. Take a look at the following example:

```php
use MiladRahimi\Jwt\Cryptography\Algorithms\Rsa\RS256Signer;
use MiladRahimi\Jwt\Cryptography\Algorithms\Rsa\RS256Verifier;
use MiladRahimi\Jwt\Cryptography\Keys\RsaPrivateKey;
use MiladRahimi\Jwt\Cryptography\Keys\RsaPublicKey;
use MiladRahimi\Jwt\Generator;
use MiladRahimi\Jwt\Parser;

$privateKey = new RsaPrivateKey('/path/to/private.pem');
$publicKey = new RsaPublicKey('/path/to/public.pem');

$signer = new RS256Signer($privateKey);
$verifier = new RS256Verifier($publicKey);

// Generate a token
$generator = new Generator($signer);
$jwt = $generator->generate(['id' => 666, 'is-admin' => true]);

// Parse the token
$parser = new Parser($verifier);
$claims = $parser->parse($jwt);

echo $claims; // ['sub' => 1, 'jti' => 2]
```

You can read [this instruction](https://en.wikibooks.org/wiki/Cryptography/Generate_a_keypair_using_OpenSSL) to learn how to generate a pair (public/private) key.

### Validation

In default, the package verifies the JWT signature, validate some of the public claims if they exist (using `DefaultValidator`), and parse the claims. If you have your custom claims, you can add their validation rules, as well. See this example:

```php
use MiladRahimi\Jwt\Parser;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Exceptions\ValidationException;
use MiladRahimi\Jwt\Validator\Rules\EqualsTo;
use MiladRahimi\Jwt\Validator\Rules\GreaterThan;

$jwt = '...'; // Get the JWT from the user

$signer = new HS256('12345678901234567890123456789012');

// Add Validation (Extend the DefaultValidator)
$validator = new DefaultValidator();
$validator->addRule('is-admin', new EqualsTo(true));
$validator->addRule('id', new GreaterThan(600));

// Parse the token
$parser = new Parser($signer, $validator);
try {
    $claims = $parser->parse($jwt);
    echo $claims; // ['sub' => 1, 'jti' => 2]
} catch (ValidationException $e) {
    // Handle error.
}
```

In the example above, we used the `DefaultValidator`. This validator has some built-in rules for public claims. We also recommend you to use it for your validation. The `DefaultValidator` is a subclass of the `BaseValidator`. You can also use the `BaseValidator` for your validations, but you will lose the built-in rules, and you have to add all the rules yourself.

#### Rules

Validators use the rules to validate the claims. Each rule determines possible values for a claim. These are the built-in rules you can find under the namespace `MiladRahimi\Jwt\Validator\Rules`:
* ConsistsOf
* EqualsTo
* GreaterThan
* GreaterThanOrEqualTo
* IdenticalTo
* LessThan
* LessThanOrEqualTo
* NewerThan
* NewerThanOrSame
* NotEmpty
* NotNull
* OlderThan
* OlderThanOrSame

You can see their description in their class doc-block.

#### Required and Optional Rules

You can add a rule to a validator as required or optional. If the rule is required, validation will fail when the claim is not present in the JWT claims.

This example demonstrates how to add rules as required and optional:

```php
$validator = new DefaultValidator();

// Add a rule as required
$validator->addRule('exp', new NewerThan(time()));

// Add a rule as required again!
$validator->addRule('exp', new NewerThan(time()), true);

// Add a rule as optional
$validator->addRule('exp', new NewerThan(time()), false);
```

#### Custom Rules

You create your own rules if the built-in ones cannot meet your needs. To create a rule, you must implement the `Rule` interface like the following example that shows `Even` rule which is going to check if the given claim is an even number or not:

```php
use MiladRahimi\Jwt\Exceptions\ValidationException;
use MiladRahimi\Jwt\Validator\Rule;

class Even implements Rule
{
    public function validate(string $name, $value)
    {
        if ($value % 2 != 0) {
            throw new ValidationException("The `$name` must an even number.");
        }
    }
}
```

### Error Handling

Here are the exceptions that the package throw:
* `InvalidKeyException`: It will be thrown by `Generator` or `Parser` when the provided key is not valid.
* `InvalidSignatureException`: It will be thrown by `Parser::parse()`, `Parser::verify()`, or `Parser::validate()` when the JWT signature is not valid.
* `InvalidTokenException`: It will be thrown by `Parser::parse()`, `Parser::verify()`, or `Parser::validate()` when the JWT format is not valid (for example it has no payload).
* `JsonDecodingException`: It will be thrown by `Parser::parse()`, or `Parser::validate()` when the JSON extracted from JWT is not valid.
* `JsonEncodingException`: It will be thrown by `Generator::generate()` when cannot convert the provided claims to JSON.
* `SigningException`: It will be thrown by `Generator::generate()` when cannot sign the token using the provided signer or key.
* `ValidationException`: It will be thrown by `Parser::parse()`, or `Parser::validate()` when one of the validation rules fail.

## License
PHP-JWT is initially created by [Milad Rahimi](http://miladrahimi.com)
and released under the [MIT License](http://opensource.org/licenses/mit-license.php).
