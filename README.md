# Librato Annotations

This class allows you to send annotations to [Librato](http://metrics.librato.com) from [Phing](http://phing.info)

I use this to mark deployments when I put my code onto production servers. I can easily see from my dashboard / graphs if the last deployment has affected performance.

## Installation

I have only tested this with installation through [Composer](http://getcomposer.org), so know guarantees whether this will work not installed through Composer.

```json
{
    "require": {
        "rcrowe/librato-annotation": "0.1.2",
        "phing/phing": "2.4.14"
    },
    "minimum-stability": "dev"
}
```

Define the the Librato task in your buildfile:

```xml
<taskdef name="librato" classname="rcrowe\Librato\AnnotationTask" />
```

## Usage

To send an annotation to Librato you need to define your username and your API key. Your API key can be found on your Librato [account page](https://metrics.librato.com/account).

Setting your detatils globally:

```xml
<property name="librato.username" value="hello@vivalacrowe.com" />
<property name="librato.password" value="12345abcdef" />
```

Doing it this way means you only have to do it once, and can then call Librarto multiple times. However, you can do this inline as well:

```xml
<librato username="hello@vivalacrowe.com" password="12345abcdef" .... />
```

### Example

```xml
<librato name="deployment" title="Deployment" desc="Deployed to live" />
```

If there is an error trying to send the annotation to Librarto the default is to carry on processing the Phing buildfile. You can change this behaviour with **haltonerror**:

```xml
<librato name="deployment" title="Deployment" desc="Deployed to live" haltonerror="true" />
```
