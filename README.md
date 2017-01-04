<style type="text/css">
	body {
		font-family: "Source Sans Pro", "Lucida Grande", sans-serif;
	}
	table {
		font-family: "Source Code Pro";
		font-size: .9em;
	}
	td {
		background-color: white;
	}
	th {
		text-align: left;
		background-color: #f8f8f8;
	}
</style>

# Email Class

Mark Simon
Share & Enjoy

A simple class to send emails.

This is a simple class file to send email using PHP. It includes the following feathers:

- HTML and/or Text Email
- Attachments
- Inline Images

## Usage

1. Create a new Email Object with or without data & options
2. Optionally, set data & options separately
3. Send

### Creating the Email Object

You can create an Email objects with options & data in the Constructor:

```php
$options=array(…);	//	See options later
$email=newEmail();
```

Alternatively, you can add the data & options after the event:

```php
$email=newEmail();
$options=array(…);	//	See options later
$email->setOptions($options);
```

Even if you create the object with data & options, you can still use `setOptions` after the event. This allows you to reuse the object with small changes.

## Options

Some options are required (see later). Internally, the default options are as follows:

```php
private $data=array(
	'smtp'=>localhost,
	'text'=>null,
	'html'=>null,
	'css'=>null,
	'images'=>null,
	'attachments'=>null,
	'to'=>null,
	'from'=>null,
	'cc'=>null,
	'bcc'=>null,
	'subject'=>null,
	'headers'=>null,
	'binary'=>null,
);
```

### Required Options

The following options are required:

- `to`
- `from`
- `subject`
- `text`

### Meaning

| option        | meaning           | example
| ------------- |:-------------|:-----|
| __to__			| to: address		| `fred@example.net`
| __from__		| from: address	| `ginger@example.net`
| __subject__		| subject			| `Test`
| __text__		| text message	| `This is a test`
| html				| HTML version of message	| `<p>This is a test</p>`
| cc				| cc: address	| `barney@example.net`
| bcc				| bcc: address	| `betty@example.net`
| images			| path to image or<br>array of paths to images	| `'…/image.jpg'`<br>`array('…' , '…')`
| attachments		| path of attachments	| `'…/document.pdf'`
| headers	| array of additional headers	| `array(`<br>`'header'=>'data',`<br>`'header'=>'data')`

### Images

To include images, you will need to:

- Ensure that you are using HTML
- Include the image(s) in your options
- Add `img` tags to your HTML

The HTML image tag should take the following form

```html
<img src="cid:…" alt="…">
```

The value of `cid` is the notional file name of the image. For example:

```html
<img src="cid:logo.png" alt="…">
```

The file name does not have to be the original file name, though it will be by default. If you want to change the file name, your image path should be something like:

	/path/to/images/something.png|logo.png

The pipe (`|`) can be use to separate the original image path from the notional name.

### Attachments

To add attachments 

### Binary Attachments

Normally, you can include attachments in the `attachment` array. However, you can also input your own attachment from binary data.

To do this, you will need to supply an array of the following:

| key | value
|------|--------------------------------
| name | Notional name of the attachment
| mime | Mime Type
| data | Actual binary datas

If you include an attachment the normal way above, this data is generated from the content.