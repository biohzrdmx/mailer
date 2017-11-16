# Mailer

Mailing abstraction layer for [Hummingbird Lite](https://github.com/biohzrdmx/hummingbird-lite), with support for custom providers.

Other flavors (WordPress maybe?) coming soon!

## Basic usage:

### Creating the message

Mailer defines a `MailerMessage` class, which holds all the required fields to describe an email message.

Just include the library and create a `MailerMessage` instance, filling the desired fields:

	# Include the library and at least one provider
	include $site->baseDir('/external/mailer.inc.php');
	include $site->baseDir('/external/provider/mandrill.provider.php');
	include $site->baseDir('/external/provider/smtp.provider.php');

	# Create a MailerMessage object and set the subject, from, to, contents, attachments, etc.
	$message = MailerMessage::newInstance()
		->setSubject('Test')
		->setFrom( array('test@mailinator' => 'Me') )
		->setTo( array('test@mailinator' => 'Me') )
		->setTemplate($site->baseDir('/parts/mail-templates/basic-email.html'))
		->setImages(array('email_header' => $site->baseDir('/images/email-header.jpg')))
		->setAttachments(array('Test.doc' => $site->baseDir('/test.doc')))
		->setReplacements(array('%email-footer%' => 'Test'))
		->setContents('<h2>Hey you</h2>');

In the example above, we added a bunch of things, such as images, attached files, replacement strings, etc.

For each `MailerMessage` instance you may define:

- **Subject** - The message's subject
- **From** - The message's sender
- **To** - The message's destination
- **Contents** - The message's contents
- **Template** - An optional HTML template to use for the message
- **Attachments** - Attached files
- **Images** - Embedded images for inline display
- **Replacements** - String replacements for your template (for example, changing %site% for the URL of your site)

That way, we've defined an abstract message that could be sent by any provider we choose.

### Sending the message

Mailer includes two providers: classic SMTP and Mandrill

To send messages using the SMTP provider you should install the [PHPMailer library](https://github.com/PHPMailer/PHPMailer) and pass the SMTP credentials through the `$options` array.

	$options = array(
		'host' => 'smtp.test.com',
		'port' => '587',
		'user' => 'smtp@test.com',
		'pass' => 'password123'
	);
	Mailer::send($message, 'smtp', $options);

*Pro tip: Download the PHPMailer ZIP and place `PHPMailerAutoload.php` and the three `class.*.php` files into `lib/PHPMailer`.*

Mandrill is even easier, you just need to install the [Mandrill PHP SDK](https://bitbucket.org/mailchimp/mandrill-api-php/downloads) and an API Key which you'll pass in the `$options` array.

	$options = array(
		'key' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'
	);
	Mailer::send($message, 'mandrill', $options);

*Pro tip: Download the Mandrill PHP SDK ZIP and extract the contents of the `src` folder into `lib/Mandrill`.*

And that kids, is how we send emails with Mailer.

## Extending the library

### Creating providers

You may refer to each of the bundled providers to see how they do work internally.

In short, you must:

- Create you provider class, extending `MailerProvider`
- Implement the `send` method in your derived class. This implementation depends on the specific provider mechanics.

We've included Mandrill and SMTP providers, but if you want to contribute by adding another provider (for example Swift Mailer or Mailgun, etc.) you're strongly encouraged to do so.

If you implement another provider please send me a push request so I can improve the providers pool.

## Credits

Lead coder: biohzrdmx <[github.com/biohzrdmx](https://github.com/biohzrdmx)>

## License

Copyright © 2017 biohzrdmx

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.